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
			->where('user_id = :userId')
			->orderBy('id', 'DESC')
			->setParameter(':userId', $userId);
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
			->where('user_id = :userId')
			->andWhere('id = :dataTypeId')
			->setParameter(':userId', $userId)
			->setParameter(':dataTypeId', $dataTypeId);
		$query->setMaxResults(100);
		$result = $query->execute();
		$data = $result->fetch();
		if($data && is_numeric($data['id']))
			return DataType::fromRow($data);

		return null;
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
			->where('user_id = :userId')
			->setParameter(':userId', $userId);
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
			->where('sddt.user_id = :userId')
			->andWhere('sddt.device_id = :deviceId')
			->orderBy('sddt.id', 'ASC')
			->setParameter(':userId', $userId)
			->setParameter(':deviceId', $deviceId);
		$query->setMaxResults(100);
		$result = $query->execute();
		$data = $result->fetchAll();

		return $data;
	}

	/**
	 * @param $userId
	 * @param $id
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function isDeletable($userId, $id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('id')
			->from('sensorlogger_device_data_types')
			->where('user_id = :userId')
			->andWhere('data_type_id = :id')
			->setParameter(':userId', $userId)
			->setParameter(':id', $id);
		if ($query->execute())
		{
			$data = $result->fetch();
			if($data && is_numeric($data['id']) && $data['id'] > 0)
				return false;
		}
		return true;
	}

	/**
	 * @param $userId
	 * @param $id
	 * @param IDBConnection $db
	 * @return bool
	 */
	// loescht data type mit angegebener id und userId, wenn id nicht in sensorlogger_device_data_typestype_id enthalten ist
	public static function deleteDataType($userId, $id, IDBConnection $db) {
		// data types nur loeschen, wenn sie von keinem Sensor mehr verwendet werden
		if (!DataTypes::isDeletable($userId, $id, $db))
			return false;
		
		$sql = 'DELETE FROM `*PREFIX*sensorlogger_data_types` WHERE user_id = ? AND id = ?';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $userId);
		$stmt->bindParam(2, $id);
		return $stmt->execute();
	}

	/**
	 * @param $userId
	 * @param $deviceId
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function deleteDeviceDataTypesByDeviceId($userId, $deviceId, IDBConnection $db) {
		// Device-DataType-Beziehungen in dieser Tabelle auf jeden Fall loeschen
		$query = $db->getQueryBuilder();
		$query->delete('sensorlogger_device_data_types')
			->where('user_id = :userId')
			->andWhere('device_id = :deviceId')
			->setParameter(':userId', $userId)
			->setParameter(':deviceId', $deviceId);
		return $query->execute();
	}
}