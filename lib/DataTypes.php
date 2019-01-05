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

	/**
	 * @param $userId
	 * @param $dataTypeId
	 * @param IDBConnection $db
	 * @return DataType
	 */
	public static function getDataTypeById($userId, $dataTypeId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select(array('id','description','type','short'))
			->from('sensorlogger_data_types')
			->where('user_id = "'.$userId.'"')
			->andWhere('id = "'.$dataTypeId.'"');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetch();
		
		if($data) {
			$data = DataType::fromRow($data);
		}

		return $data;
	}

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDataTypesByUserId($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_data_types')
			->where('user_id = "'.$userId.'"');
		$results = $query->execute();
		$data = [];
		foreach($results->fetchAll() as $result) {
			$data[] = DataType::fromRow($result);
		}
		return $data;
	}

	/**
	 * @param $userId
	 * @param $deviceId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceDataTypesByDeviceId($userId, $deviceId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select(array('sdt.id','sdt.description','sdt.type','sdt.short','sdt.type'))
			->from('sensorlogger_device_data_types','sddt')
			->leftJoin('sddt','sensorlogger_data_types','sdt', 'sdt.id = sddt.data_type_id')
			->where('sddt.user_id = "'.$userId.'"')
			->andWhere('sddt.device_id = "'.$deviceId.'" ')
			->orderBy('sddt.data_type_id', 'ASC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$data = $result->fetchAll();

		return $data;
	}

	public static function deleteDeviceDataTypesByDeviceId($deviceId, IDBConnection $db) {
        $sql = 'DELETE FROM `*PREFIX*sensorlogger_device_data_types` WHERE *PREFIX*sensorlogger_device_data_types.device_id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $deviceId);
        if($stmt->execute()){
            return true;
        }
    }
}