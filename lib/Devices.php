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
			->where('user_id = "'.$userId.'"')
			->orderBy('id', 'DESC');
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
			->where('user_id = "'.$userId.'"')
			->andWhere('id = '.$deviceId);
		$result = $query->execute();
		$data = $result->fetch();
		return $data;
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
			->where('user_id = "'.$userId.'"')
			->andWhere('uuid = "'.$deviceUuid.'"');
		$result = $query->execute();
		$data = $result->fetch();
		if (is_numeric($data['id']))
			return (int)$data['id'];
		
		return 0;
	}

	/**
	 * @param $userId
	 * @param $deviceId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceByDeviceId($userId, $deviceId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		
		$query->select(array('sdt.id','sdt.device_type_name'))
			->from('sensorlogger_devices','sd')
			->leftJoin('sd','sensorlogger_device_types','sdt', 'sdt.id = sd.type_id')
			->where('sd.user_id = "'.$userId.'"')
			->andWhere('sd.id = '.$deviceId)
			->orderBy('sd.id', 'ASC');
			
		$query->setMaxResults(100);
		$result = $query->execute();
		$data = $result->fetchAll();

		return $data;
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

		if ($stmt->execute())
			return (int)$db->lastInsertId();
		
		return -1;
		
	}
}