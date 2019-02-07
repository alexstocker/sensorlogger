<?php

namespace OCA\SensorLogger;

use OCP\IDBConnection;
//use OCA\SensorLogger\SensorDevices;

/**
 * Class SensorTypes
 *
 * @package OCA\SensorLogger
 */
class Devices {

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDevices($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select(array('id', 'user_id','uuid','name', 'type_id', 'group_id', 'group_parent_id'))
			->from('sensorlogger_devices')
			->where('user_id = :userId')
			->orderBy('id', 'DESC')
			->setParameter(':userId', $userId);
		$query->setMaxResults(100);
		$result = $query->execute();
		$data = $result->fetchAll();
		return $data;
	}

	/**
	 * @param $userId
	 * @param $deviceId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceById($userId, $deviceId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select(array('id', 'user_id', 'uuid', 'name', 'type_id', 'group_id', 'group_parent_id'))
			->from('sensorlogger_devices')
			->where('user_id = :userId')
			->andWhere('id = :deviceId')
			->setParameter(':userId', $userId)
			->setParameter(':deviceId', $deviceId);
		$result = $query->execute();
		$data = $result->fetch();
		if ($data && is_numeric($data['id']))
			return Device::fromRow($data);
		
		return null;
	}

	/**
	 * @param $userId
	 * @param $deviceUuid
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceIdByUuid($userId, $deviceUuid, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('id')
			->from('sensorlogger_devices')
			->where('user_id = :userId')
			->andWhere('uuid = :deviceUuid')
			->setParameter(':userId', $userId)
			->setParameter(':deviceUuid', $deviceUuid);
		$result = $query->execute();
		$data = $result->fetch();
		if ($data && is_numeric($data['id']))
			return (int)$data['id'];
		
		return -1;
	}

	/**
	 * @param $userId
	 * @param $deviceId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceByDeviceId($userId, $deviceId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		
		$query->selectAlias('sdt.id','id')
			->selectAlias('sdt.device_type_name','device_type_name')
			->from('sensorlogger_devices','sd')
			->leftJoin('sd','sensorlogger_device_types','sdt', 'sdt.id = sd.type_id')
			->where('sd.user_id = :userId')
			->andWhere('sd.id = :deviceId')
			->orderBy('sd.id', 'ASC')
			->setParameter(':userId', $userId)
			->setParameter(':deviceId', $deviceId);
			
		$query->setMaxResults(100);
		$result = $query->execute();
		$data = $result->fetch();
		if ($data && is_numeric($data['sdt.id']))
			return Device::fromRow($data);
		
		return null;
	}

	/**
	 * @param $userId
	 * @param $deviceId
	 * @param $deviceName
	 * @param $deviceTypeId
	 * @param IDBConnection $db
	 * @return int
	 */
	public static function insertDevice($userId, $deviceId, $deviceName, $deviceTypeId, IDBConnection $db) {
		// immer zuerst die Existenz pruefen
		$devId = Devices::getDeviceIdByUuid($userId, $deviceId, $db);
		if ($devId > 0)
			return $devId;
		
		// todo: dbms transaction
		// begin transaction 
		
		// ansonsten einfuegen
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_devices` (`uuid`,`name`,`type_id`,`user_id`) VALUES(?,?,?,?)';
		$stmt = $db->prepare($sql);
		
		if (!isset($deviceName))
			$deviceName = 'Default device';

		if (!is_numeric($deviceTypeId))
			$deviceTypeId = 0;
		
		$stmt->bindParam(1, $deviceId);
		$stmt->bindParam(2, $deviceName);
		$stmt->bindParam(3, $deviceTypeId);
		$stmt->bindParam(4, $userId);

		$lastId = -1;
		if ($stmt->execute())
			$lastId = (int)$db->lastInsertId();
		
		// Transaction end
		
		return $lastId;
	}

	/**
	 * @param $userId
	 * @param $id
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function deleteDevice($userId, $id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->delete('sensorlogger_devices')
			->where('user_id = :userId')
			->andWhere('id = :id')
			->setParameter(':userId', $userId)
			->setParameter(':id', $id);
		return $query->execute();
	}
}