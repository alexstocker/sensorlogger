<?php

namespace OCA\SensorLogger;

use OCP\AppFramework\Db\Mapper;
use OCP\AppFramework\Db\QBMapper;
use OCP\IDb;
use OCP\IDBConnection;

/**
 * Class Devices
 *
 * @package OCA\SensorLogger
 */
class Devices extends QBMapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'sensorlogger_devices', '\OCA\SensorLogger\Lib\Device');
	}

	/**
	 * @param $userId
	 * @param IDBConnection $db
	 * @return Device[]
	 */
	public static function getDevices($userId, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->selectAlias('sd.user_id','user_id')
			->selectAlias('sd.id','id')
			->selectAlias('sdt.device_type_name','device_type_name')
			->selectAlias('sdg0.device_group_name','device_group_name')
			->selectAlias('sdg1.device_group_name','device_group_parent_name')
			->from('sensorlogger_devices','sd')
			->leftJoin('sd', 'sensorlogger_device_types', 'sdt', 'sdt.id = sd.type_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg0', 'sdg0.id = sd.group_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg1', 'sdg1.id = sd.group_parent_id')
			->where('sd.user_id = "'.$userId.'"')
			->orderBy('sd.id', 'DESC');
		$query->setMaxResults(100);
		$result = $query->execute();

		$rows = $result->fetchAll();

		$data = [];
		foreach($rows as $row) {
			$data[] = Device::fromRow($row);
		}

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

		if($data) {
			$data = Device::fromRow($data);
		}

		return $data;
	}

	/**
	 * @param $userId
	 * @param $id
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function isDeletable($userId, $id, IDBConnection $db) {
        /** @var Device $device */
        $device = Devices::getDevice($userId, (int)$id, $db);
        if(SensorLogs::getLastLogByUuid($userId, $device->getUuid(), $db)) {
            return false;
        } else {
            return true;
        }
    }

	/**
	 * @param $userId
	 * @param $uuid
	 * @param IDBConnection $db
	 * @return mixed|static
	 */
	public static function getDeviceByUuid($userId, $uuid, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->from('sensorlogger_devices')
			->where('uuid = "'.$uuid.'" ')
			->andWhere('user_id = "'.$userId.'"');
		$result = $query->execute();

		$data = $result->fetch();
		if($data) {
			//foreach($data as $device) {
				$data = Device::fromRow($data);
			//}
		}

		return $data;
	}

	/**
	 * @param $userId
	 * @param $id
	 * @param IDBConnection $db
	 * @return Device
	 */
	public static function getDeviceDetails($userId, $id, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->select('*')
			->selectAlias('sdg0.device_group_name','device_group_name')
			->selectAlias('sdg1.device_group_name','device_group_parent_name')
			->from('sensorlogger_devices','sd')
			->leftJoin('sd', 'sensorlogger_device_types', 'sdt', 'sdt.id = sd.type_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg0', 'sdg0.id = sd.group_id')
			->leftJoin('sd', 'sensorlogger_device_groups', 'sdg1', 'sdg1.id = sd.group_parent_id')
			->where('sd.id = "'.$id.'" ')
			->andWhere('sd.user_id = "'.$userId.'"');
		$result = $query->execute();
		$data = $result->fetch();
		$device = Device::fromRow($data);
		return $device;
	}

	/**
	 * @param $id
	 * @param $field
	 * @param $value
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function updateDevice($id,$field,$value, IDBConnection $db) {
		$query = $db->getQueryBuilder();
		$query->update('sensorlogger_devices')
			->set($field,$query->expr()->literal($value))
			->where('id = "'.$id.'" ');
		if($query->execute()) {
			return true;
		}
	}

	/**
	 * @param $id
	 * @param IDBConnection $db
	 * @return bool
	 */
	public static function deleteDevice($id, IDBConnection $db) {
		$sql = 'DELETE FROM `*PREFIX*sensorlogger_devices` WHERE *PREFIX*sensorlogger_devices.id = ?';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $id);
		if($stmt->execute()){
			return true;
		}
	}

	/**
	 * @param $userId
	 * @param $array
	 * @param IDBConnection $db
	 * @return int|string
	 */
	public static function insertDevice($userId, $array, IDBConnection $db) {
		$sql = 'INSERT INTO `*PREFIX*sensorlogger_devices` (`uuid`,`name`,`type_id`,`user_id`) VALUES(?,?,?,?)';
		$stmt = $db->prepare($sql);

		if(isset($array['deviceId'])) {
			if(!isset($array['deviceName'])) {
				$array['deviceName'] = 'Default device';
			}

			if(!isset($array['deviceTypeId'])) {
				$array['deviceTypeId'] = 0;
			}

			$stmt->bindParam(1, $array['deviceId']);
			$stmt->bindParam(2, $array['deviceName']);
			//$stmt->bindParam(3, date('Y-m-d H:i:s'));
			$stmt->bindParam(3, $array['deviceTypeId']);
			$stmt->bindParam(4, $userId);

			if($stmt->execute()){
				return (int)$db->lastInsertId();
			}
		} else {
			return 'Missing device ID';
		}
	}

}