<?php

namespace OCA\SensorLogger\Controller;

use OC\AppFramework\Http\Request;
use OC\Security\CSP\ContentSecurityPolicy;
use OCA\SensorLogger\DataTypes;
use OCA\SensorLogger\DeviceTypes;
use OCA\SensorLogger\Log;
use OCA\SensorLogger\LogExtended;
use OCA\SensorLogger\SensorDevices;
use OCA\SensorLogger\SensorGroups;
use OCA\SensorLogger\SensorLogs;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IDb;
use OCP\IDBConnection;
use OCP\IRequest;
use OCP\IURLGenerator;

/**
 * Class SensorLoggerController
 *
 * @package OCA\SensorLogger\Controller
 */
class SensorLoggerController extends Controller {

	private $userId;

	/** @var IDBConnection */
	protected $connection;

	public function __construct($AppName,
								IRequest $request,
								IDBConnection $connection,
								IDb $db,
								$UserId) {
		parent::__construct($AppName, $request);
		$this->connection = $connection;
		$this->userId = $UserId;
		$this->db = $db;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	public function index() {
		$templateName = 'main';
		$log = SensorLogs::getLastLog($this->userId, $this->connection);
		
		$parameters = array(
				'config' => array(),
				'part' => 'dashboard',
				'log' => $log
			);
		
		$policy = new ContentSecurityPolicy();
		$policy->addAllowedFrameDomain("'self'");

		$response = new TemplateResponse($this->appName, $templateName, $parameters);
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @return TemplateResponse
	 */
	function showList() {
		$templateName = 'part.list';  // will use templates/main.php
		$logs = SensorLogs::getLogs($this->userId,$this->connection);
		$parameters = array('part' => 'list','logs' => $logs);

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedFrameDomain("'self'");

		$response = new TemplateResponse($this->appName, $templateName, $parameters,'blank');
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	/**
	 * @param int $id
	 * @return TemplateResponse
	 */
	public function showDeviceData($id) {
		$templateName = 'part.list';  // will use templates/main.php
		$logs = $this->getDeviceData($id);
		$parameters = array('part' => 'list','logs' => $logs);

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedFrameDomain("'self'");

		$response = new TemplateResponse($this->appName, $templateName, $parameters,'blank');
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	/**
	 * @param int $id
	 * @return DataResponse
	 */
	public function showDeviceDetails($id) {
		$deviceDetails = SensorDevices::getDeviceDetails($this->userId,$id,$this->connection);
		$groups = SensorGroups::getDeviceGroups($this->userId,$this->connection);
		$types = DeviceTypes::getDeviceTypes($this->userId,$this->connection);

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedFrameDomain("'self'");

		$response = $this->returnJSON(array(
			'deviceDetails' => $deviceDetails, 
			'groups' => $groups,
			'types' => $types
		));

		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	public function updateDevice($id) {
		$field = $this->request->getParam('name');
		$value = $this->request->getParam('value');
		SensorDevices::updateDevice($id,$field,$value,$this->connection);
	}

	public function createDeviceType(){
		$deviceTypeName = $this->request->getParam('device_type_name');
		$deviceTypeId = DeviceTypes::insertDeviceType($this->userId,$deviceTypeName,$this->connection);
		if(is_int($deviceTypeId)) {
			return $this->returnJSON(['deviceTypeId' => $deviceTypeId]);
		}
	}

	public function createDeviceGroup() {
		$deviceGroupName = $this->request->getParam('device_group_name');
		$deviceGroupId = SensorGroups::insertSensorGroup($this->userId,$deviceGroupName,$this->connection);
		if(is_int($deviceGroupId)) {
			return $this->returnJSON(['deviceGroupId' => $deviceGroupId]);
		}
	}

	/**
	 * @NoAdminRequired
	 * @return TemplateResponse
	 */
	public function showDashboard() {
		$templateName = 'part.dashboard';
		$log = SensorLogs::getLastLog($this->userId, $this->connection);
		$parameters = array('part' => 'dashboard','log' => $log);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @param int $id
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	public function deviceChart($id){
		$templateName = 'part.chart';
		$device = SensorDevices::getDevice($this->userId,$id,$this->connection);
		$parameters = array('part' => 'chart','device' => $device);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 * @param int $id
	 * @return string
	 */
	public function chartData($id) {
		return $this->getChartData($id);
	}

	/**
	 * @NoAdminRequired
	 */
	public function deviceList() {
		$templateName = 'part.listDevices';
		$devices = SensorDevices::getDevices($this->userId,$this->connection);
		$parameters = array('part' => 'list','devices' => $devices);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 * @return TemplateResponse
	 */
	public function deviceTypeList() {
		$templateName = 'part.listDeviceTypes';
		$deviceTypes = DeviceTypes::getDeviceTypes($this->userId,$this->connection);
		$parameters = array('part' => 'listDeviceTypes','deviceTypes' => $deviceTypes);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 * @return TemplateResponse
	 */
	public function deviceGroupList() {
		$templateName = 'part.listDeviceGroups';
		$deviceGroups = SensorGroups::getDeviceGroups($this->userId,$this->connection);
		$parameters = array('part' => 'listDeviceGroups','deviceGroups' => $deviceGroups);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @NoAdminRequired
	 * @return TemplateResponse
	 */
	public function dataTypeList() {
		$templateName = 'part.listDataTypes';
		$dataTypes = DataTypes::getDataTypes($this->userId,$this->connection);
		$parameters = array('part' => 'listDataTypes','dataTypes' => $dataTypes);
		return new TemplateResponse($this->appName, $templateName, $parameters,'blank');
	}

	/**
	 * @param $key
	 * @param $userId
	 * @return string
	 */
	public function getUserValue($key, $userId) {
		return $this->config->getUserValue($userId, $this->appName, $key);
	}

	/**
	 * @param $key
	 * @param $userId
	 * @param $value
	 */
	public function setUserValue($key, $userId, $value) {
		$this->config->setUserValue($userId, $this->appName, $key, $value);
	}

	/**
	 * @param $array
	 * @return DataResponse
	 */
	public function returnJSON($array) {
		try {
			return new DataResponse($array);
		} catch (\Exception $ex) {
			return new DataResponse(array('msg' => 'not found!'), Http::STATUS_NOT_FOUND);
		}
	}

	/**
	 * @param int $id
	 * @return string
	 */
	protected function getChartData($id) {
		$device = SensorDevices::getDevice($this->userId,$id,$this->connection);
		$logs = SensorLogs::getLogsByUuId($this->userId,$device->getUuid(),$this->connection);
		$dataTypes = DataTypes::getDeviceDataTypesByDeviceId($this->userId,$device->getId(),$this->connection);
		if(is_array($dataTypes) && !empty($dataTypes)) {
			$logs = array('logs' => $logs, 'dataTypes' => $dataTypes);
		}
		return $this->returnJSON($logs);
	}

	/**
	 * @param int $id
	 * @return array
	 */
	protected function getDeviceData($id) {
		$device = SensorDevices::getDevice($this->userId,$id,$this->connection);
		$logs = SensorLogs::getLogsByUuId($this->userId,$device['uuid'],$this->connection);
		return $logs;
	}
}