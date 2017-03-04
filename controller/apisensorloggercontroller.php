<?php

namespace OCA\SensorLogger\Controller;

use OCA\SensorLogger\SensorDevices;
use OCP\AppFramework\ApiController;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IRequest;

/**
 * Class ApiSensorLoggerController
 *
 * @package OCA\SensorLogger\Controller
 */
class ApiSensorLoggerController extends ApiController {

	private $userId;
	private $db;

	protected $config;

	public function __construct($AppName,
								IRequest $request,
								IDBConnection $db,
								IConfig $config,
								$UserId) {
		parent::__construct(
			$AppName,
			$request,
			'PUT, POST, GET, DELETE, PATCH',
			'Authorization, Content-Type, Accept',
			1728000);
		$this->db = $db;
		$this->userId = $UserId;
		$this->config = $config;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @CORS
	 */
	public function createLog() {
		$this->insertLog($this->request->getParams());
	}

	/**
	 * @param array $array
	 * @return bool
	 */
	protected function insertLog($array){
		$registered = $this->checkRegisteredDevice($array);
		if(isset($array['deviceId'])) {
			if(!$registered) {
				$registered = $this->insertDevice($array);
			}
		}
		if($registered || !isset($array['deviceId'])) {
			$deviceId = $array['deviceId'] ?: null;

			$dataJson = json_encode($array);

			$sql = 'INSERT INTO `*PREFIX*sensorlogger_logs` (temperature,humidity,created_at,user_id,device_uuid,`data`) VALUES(?,?,?,?,?,?)';
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(1, $array['temperature']);
			$stmt->bindParam(2, $array['humidity']);
			$stmt->bindParam(3, $array['date']);
			$stmt->bindParam(4, $this->userId);
			$stmt->bindParam(5, $deviceId);
			$stmt->bindParam(6, $dataJson);
			$stmt->execute();
		}
		return true;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @CORS
	 */
	public function registerDevice() {
		if(!$this->checkRegisteredDevice($this->request->getParams()) &&
			$this->checkRegisteredDevice($this->request->getParams()) !== null) {

			$lastInsertId = $this->insertDevice($this->request->getParams());
			if(is_int($lastInsertId)) {
				$this->checkRequestParams($lastInsertId,$this->request->getParams());
			}
		} else {
			return true;
		}
		return false;
	}

	/**
	 * @param array $array
	 * @return int
	 */
	protected function insertDeviceType($array) {
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_device_types` (`user_id`,`device_typ_name`) VALUES(?,?)';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $this->userId);
		$stmt->bindParam(2, $array['deviceType']);
		if($stmt->execute()){
			return (int)$this->db->lastInsertId();
		}
		return false;
	}

	/**
	 * @param string $string
	 * @return int
	 */
	protected function insertDeviceGroup($string) {
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_device_groups` (`user_id`,`device_group_name`) VALUES(?,?)';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $this->userId);
		$stmt->bindParam(2, $string);
		if($stmt->execute()){
			return (int)$this->db->lastInsertId();
		}
		return false;
	}

	/**
	 * @param array $array
	 * @return int
	 */
	protected function insertDataTypes($array) {
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_data_types` (`user_id`,`description`,`type`,`short`) VALUES(?,?,?,?)';
		$stmt = $this->db->prepare($sql);

		if(isset($array['description'])) {
			$description = $array['description'] ?: '';
		} else {
			$description = '';
		}

		if(isset($array['type'])) {
			$type = $array['type'] ?: '';
		} else {
			$type = '';
		}

		if(isset($array['unit'])) {
			$unit = $array['unit'] ?: '';
		} else {
			$unit = '';
		}

		$stmt->bindParam(1, $this->userId);
		$stmt->bindParam(2, $description);
		$stmt->bindParam(3, $type);
		$stmt->bindParam(4, $unit);
		if($stmt->execute()){
			return (int)$this->db->lastInsertId();
		}
		return false;
	}

	/**
	 * @param int $deviceId
	 * @param array $params
	 */
	protected function checkRequestParams($deviceId,$params) {
		if(isset($params['deviceType']) && !empty($params['deviceType'])) {
			$deviceTypeId = $this->insertDeviceType($params);
			if(is_int($deviceTypeId)) {
				try {
					SensorDevices::updateDevice($deviceId,'type_id',(string)$deviceTypeId,$this->db);
				} catch (\Exception $e) {}
			}
		}
		if(isset($params['deviceGroup']) && !empty($params['deviceGroup'])) {
			$deviceGroupId = $this->insertDeviceGroup($params['deviceGroup']);
			if(is_int($deviceGroupId)) {
				try {
				SensorDevices::updateDevice($deviceId,'group_id',$deviceGroupId,$this->db);
				} catch (\Exception $e) {}
			}
		}
		if(isset($params['deviceParentGroup']) && !empty($params['deviceParentGroup'])) {
			$deviceGroupParentId = $this->insertDeviceGroup($params['deviceParentGroup']);
			if(is_int($deviceGroupParentId)) {
				try {
					SensorDevices::updateDevice($deviceId, 'group_parent_id', $deviceGroupParentId, $this->db);
				} catch (\Exception $e) {
				}
			}
		}
		if(isset($params['deviceDataTypes']) && is_array($params['deviceDataTypes'])) {
			foreach($params['deviceDataTypes'] as $array){
				$dataTypeId = $this->insertDataTypes($array);
				if(is_int($dataTypeId)) {
					$this->insertDeviceDataTypes($deviceId,$dataTypeId);
				}
			}
		}
	}

	/**
	 * @param int $deviceId
	 * @param int $dataTypeId
	 */
	protected function insertDeviceDataTypes($deviceId,$dataTypeId){
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_device_data_types` (`user_id`,`device_id`,`data_type_id`) VALUES(?,?,?)';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(1, $this->userId);
		$stmt->bindParam(2, $deviceId);
		$stmt->bindParam(3, $dataTypeId);
		$stmt->execute();
	}

	/**
	 * @param $params
	 * @return bool;
	 */
	protected function checkRegisteredDevice($params) {
		if(isset($params['deviceId'])) {
			$deviceId = $params["deviceId"];
			$query = $this->db->getQueryBuilder();
			$query->select('*')
				->from('sensorlogger_devices')
				->where('uuid = "'.$deviceId.'" ');
			$query->setMaxResults(1);
			$result = $query->execute();

			$data = $result->fetchAll();

			if(count($data) < 1) {
				return false;
			} else {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param array $array
	 * @return int|string
	 */
	protected function insertDevice($array) {
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_devices` (`uuid`,`name`,`type_id`,`user_id`) VALUES(?,?,?,?)';
		$stmt = $this->db->prepare($sql);

		if(isset($array['deviceId'])) {
			if(!isset($array['deviceName'])) {
				$array['deviceName'] = 'Default device';
			}

			if(!isset($array['deviceTypeId'])) {
				$array['deviceTypeId'] = 0;
			}

			$stmt->bindParam(1, $array['deviceId']);
			$stmt->bindParam(2, $array['deviceName']);
			//$stmt->bindParam(3, date('Y-m-d H:i:s'));
			$stmt->bindParam(3, $array['deviceTypeId']);
			$stmt->bindParam(4, $this->userId);

			if($stmt->execute()){
				return (int)$this->db->lastInsertId();
			}
		} else {
			return 'Missing device ID';
		}
		return false;
	}
}