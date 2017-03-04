<?php

namespace OCA\SensorLogger;

use OCP\IDBConnection;

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
	
}