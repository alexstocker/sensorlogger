<?php
/**
 * Created by PhpStorm.
 * User: elex
 * Date: 02.03.17
 * Time: 20:34
 */

namespace OCA\SensorLogger;


use OCP\IDBConnection;

class DataTypes {

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDataTypes($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select(array('id','description','type','short'))
			->from('sensorlogger_data_types')
			->where('user_id = "'.$userId.'"')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}
}