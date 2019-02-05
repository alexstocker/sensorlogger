<?php

namespace OCA\SensorLogger;

use OCP\IDBConnection;

/**
 * Class SensorGroups
 *
 * @package OCA\SensorLogger
 */
class SensorGroups{

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceGroups($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_device_groups')
			->where('user_id = "'.$userId.'"')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();
		$data = $result->fetchAll();
		return $data;
	}

	/**
	 * @param $userId
	 * @param $groupName
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceGroupByName($userId, $groupName, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_device_groups')
			->where('user_id = "'.$userId.'"')
			->andWhere('device_group_name = "'.$groupName.'"');
		$result = $query->execute();
		$data = $result->fetch();
		return $data;
	}

	# TODO [GH6] Add SensorGroup::delete

	public static function insertSensorGroup($userId, $deviceGroupName, IDBConnection $db) {
		// immer zuerst die Existenz pruefen
		$devGroup = SensorGroups::getDeviceGroupByName($userId, $deviceGroupName, $db);
		if (is_numeric($devGroup['id']) && $devGroup['id'] > 0)
			return (int)$devGroup['id'];
		
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_device_groups` (`user_id`,`device_group_name`) VALUES(?,?)';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $userId);
		$stmt->bindParam(2, $deviceGroupName);
		if($stmt->execute()){
			return (int)$db->lastInsertId();
		}
		return 0;
	}
	
}