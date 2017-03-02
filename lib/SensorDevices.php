<?php
/**
 * Created by PhpStorm.
 * User: elex
 * Date: 19.02.17
 * Time: 15:29
 */

namespace OCA\SensorLogger;

use OCP\IDBConnection;

/**
 * Class SensorDevices
 *
 * @package OCA\SensorLogger
 */
class SensorDevices {

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

}