<?php

namespace OCA\SensorLogger;

use OCP\IDBConnection;

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
			->orderBy('id', 'DESC');
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
			->orderBy('id', 'DESC');
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
     * @param int $limit
     * @param int $offset
     * @return array
     */
	public static function getLogsByUuId($userId, $uuId, IDBConnection $db, $limit = 1000, $offset = 0) {
		$query = $db->getQueryBuilder();
		$query->select(array('id','device_uuid','humidity','temperature','data','created_at'))
			->from('sensorlogger_logs')
			->where('device_uuid = "'.$uuId.'"')
			->andWhere('user_id = "'.$userId.'"')
			->orderBy('id', 'DESC');
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

	/**
	 * @param $id
	 * @param IDBConnection $db
	 * @return \Doctrine\DBAL\Driver\Statement|int
	 */
	public static function deleteLogById($id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->delete('sensorlogger_logs')
			//->where('id = :log_id')
			//->setParameter([':log_id' => ''])
			->where('id = :log_id')
			->setParameters([
				':log_id' => $id
			]);
		return $query->execute();
	}

    /**
     * @param $uuid
     * @param IDBConnection $db
     * @return \Doctrine\DBAL\Driver\Statement|int
     */
    public static function deleteLogsByUuid($uuid, IDBConnection $db) {
        $query = $db->getQueryBuilder();
        $query->delete('sensorlogger_logs')
            ->where('device_uuid = :uuid')
            ->setParameters([
                ':uuid' => $uuid
            ]);
        return $query->execute();
    }
	
	// insert log atomar
	/*
	SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;
	START TRANSACTION;
		 insert into
		 get last id
	COMMIT;
	*/
}