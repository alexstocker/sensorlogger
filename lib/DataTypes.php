<?php

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