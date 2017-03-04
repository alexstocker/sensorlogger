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
		$query->select(array('id','device_typ_name'))
			->from('sensorlogger_device_types')
			->where('user_id = "'.$userId.'"')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}
}