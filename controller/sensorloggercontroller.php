<?php

namespace OCA\SensorLogger\Controller;

use OC\Core\Command\Background\Ajax;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Encryption\IEncryptionModule;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IRequest;
use OCP\IURLGenerator;


class SensorLoggerController extends Controller {

	private $userId;

	/** @var IDBConnection */
	protected $connection;

	protected $config;

	private $urlGenerator;

	//protected $sensorLog;

	public function __construct($AppName,
								IRequest $request,
								IDBConnection $connection,
								IConfig $config,
								IURLGenerator $urlGenerator,
								$UserId){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->connection = $connection;
		$this->config = $config;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	public function index() {
		if(!$this->getUserValue('apiKey',$this->userId)) {
			$this->setUserValue('apiKey',$this->userId,$this->generateApiKey());
			$this->setUserValue('sharedSecret',$this->userId,$this->generateSharedSecret());
		}

		$templateName = 'main';  // will use templates/main.php
		$logs = $this->getLastLog();
		$parameters = array(
				'config' => array(
					'apiKey' => $this->getUserValue('apiKey',$this->userId),
					'sharedSecret' => $this->getUserValue('sharedSecret', $this->userId)),
				'part' => 'dashboard',
				'logs' => $logs
			);
		return new TemplateResponse($this->appName, $templateName, $parameters);
	}

	/**
	 * @NoAdminRequired
	 */
	function showList() {
		$templateName = 'part.list';  // will use templates/main.php
		$logs = $this->getLogs();
		$parameters = array('part' => 'list','logs' => $logs);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 */
	public function showDashboard() {
		$templateName = 'part.dashboard';  // will use templates/main.php
		$logs = $this->getLastLog();
		$parameters = array('part' => 'dashboard','logs' => $logs);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	public function deviceChart($id){
		$templateName = 'part.chart';  // will use templates/main.php
		$device = $this->getDevice($id);
		$parameters = array('part' => 'chart','device' => $device);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 */
	public function chartData($id) {
		return $this->getChartData($id);
	}

	protected function getChartData($id) {
		$device = $this->getDevice($id);
		
		$logs = $this->getLogsByUuId($device['uuid']);

		return json_encode($logs);
	}

	/**
	 * @NoAdminRequired
	 */
	public function deviceList() {
		$templateName = 'part.listDevices';  // will use templates/main.php
		$devices = $this->getDevices();
		$parameters = array('part' => 'list','devices' => $devices);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 */
	public function deviceTypeList() {
		$templateName = 'part.listDeviceTypes';  // will use templates/main.php
		$deviceTypes = $this->getDeviceTypes();
		$parameters = array('part' => 'listDeviceTypes','deviceTypes' => $deviceTypes);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 */
	public function deviceGroupList() {
		$templateName = 'part.listDeviceGroups';  // will use templates/main.php
		$deviceGroups = $this->getDeviceGroups();
		$parameters = array('part' => 'listDeviceGroups','deviceGroups' => $deviceGroups);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 */
	public function dataTypeList() {
		$templateName = 'part.listDataTypes';  // will use templates/main.php
		$dataTypes = $this->getDataTypes();
		$parameters = array('part' => 'listDataTypes','dataTypes' => $dataTypes);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	protected function getDevice($id) {
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->where('id = "'.$id.'" ');
		$result = $query->execute();

		$data = $result->fetch();
		return $data;
	}

	protected function getDevices() {
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}

	protected function getDeviceTypes() {
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_device_types')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}

	protected function getDataTypes() {
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_data_types')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}

	protected function getDeviceGroups() {
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_device_groups')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}

	protected function getLogs() {
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_logs')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}
	
	protected function getLogsByUuId($uuId) {
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_logs')
			->where('device_uuid = "'.$uuId.'"')
			->orderBy('created_at', 'DESC');
		$query->setMaxResults(1000);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}

	protected function getLastLog() {
		$query = $this->connection->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_logs')
			->orderBy('created_at', 'DESC');
		$query->setMaxResults(1);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}

	public function getUserValue($key, $userId) {
		return $this->config->getUserValue($userId, $this->appName, $key);
	}
	public function setUserValue($key, $userId, $value) {
		$this->config->setUserValue($userId, $this->appName, $key, $value);
	}

	protected function generateApiKey($strong = true) {
		return bin2hex(openssl_random_pseudo_bytes(64,$strong));
	}

	protected function generateSharedSecret($strong = true) {
		return bin2hex(openssl_random_pseudo_bytes(32,$strong));
	}


}