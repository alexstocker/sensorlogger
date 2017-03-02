<?php
/**
 * Created by PhpStorm.
 * User: elex
 * Date: 19.02.17
 * Time: 15:28
 */

namespace OCA\SensorLogger\Controller;

use OCP\AppFramework\ApiController;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IRequest;

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
	 * @CORS
	 */
	public function index() {

	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @CORS
	 */
	public function createLog() {
		$this->insertLog($this->request->getParams());
	}

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
			//$stmt->bindParam(3, date('Y-m-d H:i:s'));
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
			return $this->insertDevice($this->request->getParams());
		} else {
			return true;
		}
	}

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
		return null;
	}

	protected function insertDevice($array) {
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_devices` (`uuid`,`name`,`type`) VALUES(?,?,?)';
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
			$stmt->execute();
			return true;
		} else {
			return 'Missing device ID';
		}
	}
}