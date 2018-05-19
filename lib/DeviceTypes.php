<?php

namespace OCA\SensorLogger;

use OCP\IDBConnection;

/**
 * Class SensorTypes
 *
 * @package OCA\SensorLogger
 */
class DeviceTypes {

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceTypes($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('sdt.user_id','sdt.id','sdt.device_type_name')
			->from('sensorlogger_device_types','sdt')
			->leftJoin('sdt', 'sensorlogger_devices', 'sd', 'sdt.id = sd.type_id')
			->where('sdt.user_id = "'.$userId.'"')
			->groupBy('sdt.id')
			->orderBy('sdt.id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}

	public static function insertDeviceType($userId, $deviceTypeName, IDBConnection $db) {
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_device_types` (`user_id`,`device_type_name`) VALUES(?,?)';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $userId);
		$stmt->bindParam(2, $deviceTypeName);
		if($stmt->execute()){
			return (int)$db->lastInsertId();
		}
		return false;
	}
}