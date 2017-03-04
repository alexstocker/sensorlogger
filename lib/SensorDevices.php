<?php

namespace OCA\SensorLogger;

use OCP\AppFramework\Db\Mapper;
use OCP\IDb;
use OCP\IDBConnection;

/**
 * Class SensorDevices
 *
 * @package OCA\SensorLogger
 */
class SensorDevices extends Mapper {

	public function __construct(IDb $db) {
		parent::__construct($db, 'sensorlogger_devices', '\OCA\SensorLogger\Lib\Device');
	}
	
	public function testQuery($userId, $id, IDBConnection $db){
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->where('id = "'.$id.'" ')
			->andWhere('user_id = "'.$userId.'"');
		//$result = $query->execute();

		$entities = $this->findEntities($query->getSQL());
	}

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDevices($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->where('user_id = "'.$userId.'"')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();
		return $data;
	}

	/**
	 * @param $userId
	 * @param $id
	 * @param IDBConnection $db
	 * @return mixed
	 */
	public static function getDevice($userId, $id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->where('id = "'.$id.'" ')
			->andWhere('user_id = "'.$userId.'"');
		$result = $query->execute();

		$data = $result->fetch();

		return $data;
	}
	
	public static function getDeviceDetails($userId, $id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->where('id = "'.$id.'" ')
			->andWhere('user_id = "'.$userId.'"');
		$result = $query->execute();
		$data = $result->fetch();
		$device = Device::fromRow($data);
		return $device;
	}
	
	public static function updateDevice($id,$field,$value, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->update('sensorlogger_devices')
			->set($field,$query->expr()->literal($value))
			->where('id = "'.$id.'" ');

		if($query->execute()) {
			return true;
		}
	}

	public static function insertDevice($userId, $array, IDBConnection $db) {
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_devices` (`uuid`,`name`,`type_id`,`user_id`) VALUES(?,?,?,?)';
		$stmt = $db->prepare($sql);

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
			$stmt->bindParam(4, $userId);

			if($stmt->execute()){
				return (int)$db->lastInsertId();
			}
		} else {
			return 'Missing device ID';
		}
	}

}