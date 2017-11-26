<?php

namespace OCA\SensorLogger\Controller;

use OC\OCS\Exception;
use OC\Security\CSP\ContentSecurityPolicy;
use OCA\SensorLogger\DataTypes;
use OCA\SensorLogger\Device;
use OCA\SensorLogger\DeviceTypes;
use OCA\SensorLogger\SensorDevices;
use OCA\SensorLogger\SensorGroups;
use OCA\SensorLogger\SensorLogs;
use OCA\SensorLogger\Widgets;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IL10N;
use OCP\INavigationManager;
use OCP\IRequest;
use OCP\IURLGenerator;

/**
 * Class SensorLoggerController
 *
 * @package OCA\SensorLogger\Controller
 */
class SensorLoggerController extends Controller {

	/**
	 * @var
	 */
	private $userId;

	/** @var IDBConnection */
	protected $connection;

	/**
	 * @var IConfig
	 */
	protected $config;

	protected $urlGenerator;
	protected $navigationManager;
	protected $l10n;

	/**
	 * SensorLoggerController constructor.
	 *
	 * @param string $AppName
	 * @param IRequest $request
	 * @param IURLGenerator $urlGenerator
	 * @param INavigationManager $navigationManager
	 * @param IL10N $l10n
	 * @param IDBConnection $connection
	 * @param IConfig $config
	 * @param $UserId
	 */
	public function __construct($AppName,
									IRequest $request,
									IURLGenerator $urlGenerator,
									INavigationManager $navigationManager,
									IL10N $l10n,
									IDBConnection $connection,
									IConfig $config,
									$UserId) {
		parent::__construct($AppName, $request);
		$this->connection = $connection;
		$this->userId = $UserId;
		$this->config = $config;
		$this->urlGenerator = $urlGenerator;
		$this->navigationManager = $navigationManager;
		$this->l10n = $l10n;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	public function index() {
		$templateName = 'main';
		$widgets = $this->getWidgets();



		$parameters = array(
				'part' => 'dashboard',
				'widgets' => $widgets,
				'navItems' => $this->getNavigationItems()
			);

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedFrameDomain("'self'");

		$response = new TemplateResponse($this->appName, $templateName, $parameters);
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	protected function getNavigationItems() {
		$navItems = \OCA\SensorLogger\App::getNavigationManager()->getAll();
		usort($navItems, function($item1, $item2) {
			return $item1['order'] - $item2['order'];
		});

		foreach($navItems as $navIdx => $navValues) {
			if($navValues['appName'] !== 'sensorlogger') {
				unset($navItems[$navIdx]);
			}
		}

		return $navItems;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function sharingIn(){
		# TODO [GH18]implement sensorloggercontroller::sharingIn
		$templateName = 'main';  // will use templates/main.php
		$parameters = array(
			'part' => 'listSharedDevices',
			'devices' => [],
			'navItems' => $this->getNavigationItems());

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedFrameDomain("'self'");

		$response = new TemplateResponse($this->appName, $templateName, $parameters);
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function sharingOut(){
		#TODO [GH19] implement sensorloggercontroller::sharingOut
		$templateName = 'main';  // will use templates/main.php
		$parameters = array(
			'part' => 'listSharedDevices',
			'devices' => [],
			'navItems' => $this->getNavigationItems());

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedFrameDomain("'self'");

		$response = new TemplateResponse($this->appName, $templateName, $parameters);
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function sharedLink(){
		# TODO [GH20] imnplement sensorloggercontroller::sharedLink
		$templateName = 'main';  // will use templates/main.php
		$parameters = array(
			'part' => 'listSharedDevices',
			'devices' => [],
			'navItems' => $this->getNavigationItems());

		$policy = new ContentSecurityPolicy();
		$policy->addAllowedFrameDomain("'self'");

		$response = new TemplateResponse($this->appName, $templateName, $parameters);
		$response->setContentSecurityPolicy($policy);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @return Widgets
	 */
	protected function getWidgets(){
		$devices = SensorDevices::getDevices($this->userId,$this->connection);
		$widgets = [];
		foreach ($devices as $device) {
			foreach(Widgets::WIDGET_TYPES as $key => $value) {

				$widgetConfig = json_decode($this->getUserValue(
					'widget-'.$key.'-'.$device->getId(),
					$this->userId));

				if($widgetConfig === null) {
					continue;
				}
				$buildWidget = Widgets::build($this->userId, $device, $widgetConfig, $this->connection, $this->config);
				$widgets[] = $buildWidget;
			}
		}
		return $widgets;
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
	 * @NoAdminRequired
	 * @return DataResponse
	 */
	public function getWidgetTypes() {
		$widgetTypes = Widgets::WIDGET_TYPES;
		$devices = SensorDevices::getDevices($this->userId,$this->connection);
		return $this->returnJSON(array('widgetTypes' => $widgetTypes, 'devices' => $devices));
	}

	/**
	 * @NoAdminRequired
	 * @return DataResponse
	 */
	public function createWidget(){
		$array = $this->request->getParams();
		$widgetId = $this->request->getParam('device_id');
		$widgetType = $this->request->getParam('widget_type');
		$json = json_encode($array);
		if(!$widgetId || !$widgetType) {
			return $this->returnJSON(array('errors' => 'Could not create widget!'));
		}
		try {
			$this->setUserValue('widget-'.$widgetType.'-'.$widgetId,$this->userId,$json);
			return $this->returnJSON(array('id' => 'widget-'.$widgetType.'-'.$widgetId));
		} catch (Exception $e) {
			return $this->returnJSON(array('errors' => 'Could not create widget!'));
		}
	}

	/**
	 * @NoAdminRequired
	 * @param $id
	 * @return DataResponse
	 */
	public function deleteWidget($id) {
		try {
			$this->config->deleteUserValue($this->userId,$this->appName,$id);
			return $this->returnJSON(array('success' => true));
		} catch (Exception $e) {
			return $this->returnJSON(array('success' => false));
		}
	}

	/**
	 * @NoAdminRequired
	 * @param $id
	 * @return DataResponse
	 */
	public function deleteDevice($id)
    {
        if (SensorDevices::isDeletable($this->userId, (int)$id, $this->connection)) {
            try {
                SensorDevices::deleteDevice((int)$id, $this->connection);
                DataTypes::deleteDeviceDataTypesByDeviceId((int)$id, $this->connection);
                return $this->returnJSON(array('success' => true));
            } catch (Exception $e) {}
        }
        return $this->returnJSON(array('success' => false));
	}

	/**
	 * @NoAdminRequired
	 * @param int $id
	 * @return TemplateResponse
	 */
	public function showDeviceData($id) {

		# TODO [GH26] Rework sensorloggercontroller::showDeviceData
		# and rework template too
		
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
	 * @NoAdminRequired
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

	/**
	 * @NoAdminRequired
	 * @param $id
	 */
	public function updateDevice($id) {
		$field = $this->request->getParam('name');
		$value = $this->request->getParam('value');
		SensorDevices::updateDevice($id,$field,$value,$this->connection);
	}

	/**
	 * @NoAdminRequired
	 * @return DataResponse
	 */
	public function createDeviceType(){
		$deviceTypeName = $this->request->getParam('device_type_name');
		$deviceTypeId = DeviceTypes::insertDeviceType($this->userId,$deviceTypeName,$this->connection);
		if(is_int($deviceTypeId)) {
			return $this->returnJSON(['deviceTypeId' => $deviceTypeId]);
		}
	}

	/**
	 * @NoAdminRequired
	 * @return DataResponse
	 */
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
		$widgets = $this->getWidgets();
		$parameters = array('part' => 'dashboard','log' => $log, 'widgets' => $widgets);
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
		$logs = SensorLogs::getLogsByUuId($this->userId,$device->getUuid(),$this->connection);
		return $logs;
	}
}