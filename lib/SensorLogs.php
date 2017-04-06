<?php

namespace OCA\SensorLogger;

use OCP\AppFramework\Controller;
use OCP\IConfig;
use \OCP\IDb;
use OCP\IDBConnection;
use OCP\IL10N;

/**
 * Class SensorLogs
 *
 * @package OCA\SensorLogger
 */
class SensorLogs {

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return Log
	 */
	public static function getLastLog($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_logs')
			->where('user_id = "'.$userId.'"')
			->orderBy('created_at', 'DESC');
		$query->setMaxResults(1);
		$result = $query->execute();

		$data = $result->fetch();

		if($data){
			$data = Log::fromRow($data);
		}
		return $data;
	}

	public static function getLastLogByUuid($userId, $deviceId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_logs')
			->where('user_id = "'.$userId.'"')
			->andWhere('device_uuid = "'.$deviceId.'"')
			->orderBy('created_at', 'DESC');
		$query->setMaxResults(1);
		$result = $query->execute();

		$data = $result->fetch();

		if($data){
			$data = Log::fromRow($data);
		}
		return $data;
	}

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getLogs($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_logs')
			->where('user_id = "'.$userId.'"')
			->orderBy('id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();
		$data = $result->fetchAll();

		$logs = [];
		if($data) {
			foreach($data as $log) {
				$logModel = Log::fromRow($log);
				$logs[] = $logModel;
			}
		}
		return $logs;
	}

	/**
	 * @param $userId
	 * @param $uuId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getLogsByUuId($userId, $uuId, IDBConnection $db, $limit = 1000, $offset = 0) {
		$query = $db->getQueryBuilder();
		$query->select(array('id','device_uuid','humidity','temperature','data','created_at'))
			->from('sensorlogger_logs')
			->where('device_uuid = "'.$uuId.'"')
			->andWhere('user_id = "'.$userId.'"')
			->orderBy('created_at', 'DESC');
		$query->setMaxResults($limit);
		$query->setFirstResult($offset);
		$result = $query->execute();

		$data = $result->fetchAll();

		$logs = [];
		if($data) {
			foreach($data as $log) {
				$logModel = Log::fromRow($log);
				$logs[] = $logModel;
			}
		}
		return $logs;
	}
	
	/*
	public function getUserValue($key, $userId) {
		return $this->config->getUserValue($userId, $this->appName, $key);
	}
	public function setUserValue($key, $userId, $value) {
		$this->config->setUserValue($userId, $this->appName, $key, $value);
	}
	*/
}