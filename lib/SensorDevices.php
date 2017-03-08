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
	 * @return Device[]
	 */
	public static function getDevices($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->selectAlias('sd.user_id','user_id')
			->selectAlias('sd.id','id')
			->selectAlias('sdg0.device_group_name','device_group_name')
			->selectAlias('sdg1.device_group_name','device_group_parent_name')
			->from('sensorlogger_devices','sd')
			->leftJoin('sd', 'sensorlogger_device_types', 'sdt', 'sdt.id = sd.type_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg0', 'sdg0.id = sd.group_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg1', 'sdg1.id = sd.group_parent_id')
			->where('sd.user_id = "'.$userId.'"')
			->orderBy('sd.id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$rows = $result->fetchAll();

		$data = [];
		foreach($rows as $row) {
			$data[] = Device::fromRow($row);
		}

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

		if($data) {
			$data = Device::fromRow($data);
		}

		return $data;
	}

	public static function getDeviceByUuid($userId, $uuid, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->where('uuid = "'.$uuid.'" ')
			->andWhere('user_id = "'.$userId.'"');
		$result = $query->execute();

		$data = $result->fetch();
		if($data) {
			//foreach($data as $device) {
				$data = Device::fromRow($data);
			//}
		}

		return $data;
	}

	public static function getDeviceDetails($userId, $id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->selectAlias('sdg0.device_group_name','device_group_name')
			->selectAlias('sdg1.device_group_name','device_group_parent_name')
			->from('sensorlogger_devices','sd')
			->leftJoin('sd', 'sensorlogger_device_types', 'sdt', 'sdt.id = sd.type_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg0', 'sdg0.id = sd.group_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg1', 'sdg1.id = sd.group_parent_id')
			->where('sd.id = "'.$id.'" ')
			->andWhere('sd.user_id = "'.$userId.'"');
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