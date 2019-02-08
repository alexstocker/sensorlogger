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
		$query->select(array('sdt.user_id','sdt.id','sdt.device_type_name'))
			->from('sensorlogger_device_types','sdt')
			->leftJoin('sdt', 'sensorlogger_devices', 'sd', 'sdt.id = sd.type_id')
			->where('sdt.user_id = :userId')
			->groupBy('sdt.id')
			->orderBy('sdt.id', 'DESC')
			->setParameter(':userId', $userId);
		$query->setMaxResults(100);
		$result = $query->execute();
		$data = $result->fetchAll();
		return $data;
	}

	/**
	 * @param $userId
	 * @param $deviceTypeId
	 * @param IDBConnection $db
	 * @return string
	 */
	public static function getDeviceType($userId, $deviceTypeId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select(array('id', 'user_id', 'device_type_name'))
			->from('sensorlogger_device_types')
			->where('user_id = :userId')
			->andWhere('id = :deviceTypeId')
			->setParameter(':userId', $userId)
			->setParameter(':deviceTypeId', $deviceTypeId);
		$result = $query->execute();
		if ($result)
		{
			$data = $result->fetch();
			if ($data && is_numeric($data['id']))
				return $data;
		}
		return null;
	}

	/**
	 * @param $userId
	 * @param $deviceTypeName
	 * @param IDBConnection $db
	 * @return int
	 */
	public static function getDeviceTypeByName($userId, $deviceTypeName, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select(array('id', 'user_id', 'device_type_name'))
			->from('sensorlogger_device_types')
			->where('user_id = :userId')
			->andWhere('device_type_name = :deviceTypeName')
			->setParameter(':userId', $userId)
			->setParameter(':deviceTypeName', $deviceTypeName);
		$result = $query->execute();
		if ($result)
		{
			$data = $result->fetch();
			if ($data && is_numeric($data['id']))
				return $data;
		}
		return null;
	}

	/**
	 * @param $userId
	 * @param $deviceId
	 * @param IDBConnection $db
	 * @return array
	 */
	public static function getDeviceTypesByDeviceId($userId, $deviceId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		
		$query->select(array('sdt.id','sdt.device_type_name'))
			->from('sensorlogger_devices','sd')
			->leftJoin('sd','sensorlogger_device_types','sdt', 'sdt.id = sd.type_id')
			->where('sd.user_id = :userId')
			->andWhere('sd.id = :deviceId')
			->orderBy('sd.id', 'ASC')
			->setParameter(':userId', $userId)
			->setParameter(':deviceId', $deviceId);
			
		$query->setMaxResults(100);
		$result = $query->execute();
		if ($result)
			return $result->fetchAll();
		return null;
	}

	/**
	 * @param $userId
	 * @param $deviceTypeName
	 * @param IDBConnection $db
	 * @return int
	 */
	public static function insertDeviceType($userId, $deviceTypeName, IDBConnection $db) {
		// zuerst immer einen vorhandenen Type suchen
		$devType = DeviceTypes::getDeviceTypeByName($userId, $deviceTypeName, $db);
		if (is_numeric($devType['id']) && $devType['id'] > 0)
			return (int)$devType['id'];
		
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_device_types` (`user_id`,`device_type_name`) VALUES(?,?)';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $userId);
		$stmt->bindParam(2, $deviceTypeName);
		if($stmt->execute())
			return (int)$db->lastInsertId();

		return -1;
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
			->from('sensorlogger_devices')
			->where('user_id = :userId')
			->andWhere('type_id = :id')
			->setParameter(':userId', $userId)
			->setParameter(':id', $id);
		$result = $query->execute();
		if ($result)
		{
			$data = $result->fetch();
			if($data && is_numeric($data['id']) && $data['id'] > 0)
				return false;
		}
		return true;
	}

	/**
	 * @param $userId
	 * @param $typeId
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function deleteDeviceType($userId, $typeId, IDBConnection $db) {
		// DeviceType nur loeschen, wenn nicht noch in Tabelle Devices enthalten
		if (!DeviceTypes::isDeletable($userId, $typeId, $db))
			return false;
		
		$query = $db->getQueryBuilder();
		$query->delete('sensorlogger_device_types')
			->where('user_id = :userId')
			->andWhere('id = :typeId')
			->setParameter(':userId', $userId)
			->setParameter(':typeId', $typeId);
		return $query->execute();
    }
}