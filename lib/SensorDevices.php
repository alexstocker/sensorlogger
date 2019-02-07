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
			->selectAlias('sdt.device_type_name','device_type_name')
			->selectAlias('sdg0.device_group_name','device_group_name')
			->selectAlias('sdg1.device_group_name','device_group_parent_name')
			->from('sensorlogger_devices','sd')
			->leftJoin('sd', 'sensorlogger_device_types', 'sdt', 'sdt.id = sd.type_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg0', 'sdg0.id = sd.group_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg1', 'sdg1.id = sd.group_parent_id')
			->where('sd.user_id = :userId')
			->orderBy('sd.id', 'DESC')
			->setParameter(':userId', $userId);
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
			->where('id = :id')
			->andWhere('user_id = :userId')
			->setParameter(':id', $id)
			->setParameter(':userId', $userId);
		$result = $query->execute();
		$data = $result->fetch();
		if($data) {
			$data = Device::fromRow($data);
		}

		return $data;
	}

	/**
	 * @param $userId
	 * @param $id
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function isDeletable($userId, $id, IDBConnection $db) {
        /** @var Device $device */
        $device = SensorDevices::getDevice($userId, (int)$id, $db);
        if(SensorLogs::getLastLogByUuid($userId, $device->getUuid(), $db))
            return false;
		
        return true;
    }

	/**
	 * @param $userId
	 * @param $uuid
	 * @param IDBConnection $db
	 * @return mixed|static
	 */
	public static function getDeviceByUuid($userId, $uuid, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->where('uuid = :uuid" ')
			->andWhere('user_id = :userId')
			->setParameter(':uuid', $uuid)
			->setParameter(':userId', $userId);
		$result = $query->execute();
		$data = $result->fetch();
		if($data) {
			//foreach($data as $device) {
				$data = Device::fromRow($data);
			//}
		}
		return $data;
	}

	/**
	 * @param $userId
	 * @param $id
	 * @param IDBConnection $db
	 * @return Device
	 */
	public static function getDeviceDetails($userId, $id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->selectAlias('sdg0.device_group_name','device_group_name')
			->selectAlias('sdg1.device_group_name','device_group_parent_name')
			->from('sensorlogger_devices','sd')
			->leftJoin('sd', 'sensorlogger_device_types', 'sdt', 'sdt.id = sd.type_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg0', 'sdg0.id = sd.group_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg1', 'sdg1.id = sd.group_parent_id')
			->where('sd.id = :id')
			->andWhere('sd.user_id = :userId')
			->setParameter(':id', $id)
			->setParameter(':userId', $userId);
		$result = $query->execute();
		$data = $result->fetch();
		$device = Device::fromRow($data);
		return $device;
	}

	/**
	 * @param $id
	 * @param $field
	 * @param $value
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function updateDevice($id, $field, $value, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->update('sensorlogger_devices')
			->set($field,$query->expr()->literal($value))
			->where('id = :id')
			->setParameter(':id', $id);
		return $query->execute();
	}

	/**
	 * @param $id
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function deleteDevice($id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->delete('sensorlogger_devices')
			->where('id = :id')
			->setParameter(':id', $id);
		return $query->execute();
	}

	/**
	 * @param $userId
	 * @param $array
	 * @param IDBConnection $db
	 * @return int|string
	 */
	public static function insertDevice($userId, $array, IDBConnection $db) {
/*
		// immer zuerst die Existenz pruefen
		$deviceId = isset($array['deviceId']) ? (int)$array['deviceId'] : 0;
		$device = SensorDevices::getDeviceByUuid($userId, $deviceId, $db);
		if ($device->getId() > 0)
			return $devId;
*/		
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