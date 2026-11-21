<?php

namespace OCA\SensorLogger;

use OCP\IDBConnection;

/**
 * Class SensorGroups
 *
 * @package OCA\SensorLogger
 */
class DeviceGroups
{

    /**
     * @param $userId
     * @param IDBConnection $db
     * @return array or null
     */
    public static function getDeviceGroups($userId, IDBConnection $db)
    {
        $query = $db->getQueryBuilder();
        $query->select('*')
            ->from('sensorlogger_device_groups')
            ->where('user_id = :userId')
            ->orderBy('id', 'DESC')
            ->setParameter('userId', $userId);

        return $db->executeQuery(
            $query->getSQL(),
            $query->getParameters(),
            $query->getParameterTypes()
        )->fetchAllAssociative();
    }

    /**
     * @param $userId
     * @param $groupId
     * @param IDBConnection $db
     * @return array or null
     */
    public static function getDeviceGroupById($userId, $groupId, IDBConnection $db)
    {
        $query = $db->getQueryBuilder();
        $query->select('*')
            ->from('sensorlogger_device_groups')
            ->where('id = :groupId')
            ->andWhere('user_id = :userId')
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId);

        return $db->executeQuery(
            $query->getSQL(),
            $query->getParameters(),
            $query->getParameterTypes()
        )->fetchAssociative();
    }

    /**
     * @param $userId
     * @param $groupName
     * @param IDBConnection $db
     * @return array or null
     */
    public static function getDeviceGroupByName($userId, $groupName, IDBConnection $db)
    {
        $query = $db->getQueryBuilder();
        $query->select('*')
            ->from('sensorlogger_device_groups')
            ->where('device_group_name = :groupName')
            ->andWhere('user_id = :userId')
            ->setParameter('groupName', $groupName)
            ->setParameter('userId', $userId);

        return $db->executeQuery(
            $query->getSQL(),
            $query->getParameters(),
            $query->getParameterTypes()
        )->fetchAssociative();
    }

    /**
     * @param $userId
     * @param $id
     * @param IDBConnection $db
     * @return bool
     */
    public static function isDeletable($userId, $id, IDBConnection $db)
    {
        $query = $db->getQueryBuilder();
        $query->select('id')
            ->from('sensorlogger_devices')
            ->where('user_id = :userId')
            ->andWhere('( group_id = :gid')
            ->orWhere('group_parent_id = :gpid )')
            ->setParameter('userId', $userId)
            ->setParameter('gid', $id)
            ->setParameter('gpid', $id);
        $data = $db->executeQuery(
            $query->getSQL(),
            $query->getParameters(),
            $query->getParameterTypes()
        )->fetchAssociative();
        if ($data && is_numeric($data['id']) && $data['id'] > 0) {
            return false;
        }
        return true;
    }

    //# TODO [GH6] Add SensorGroup::delete

    /**
     * @param $userId
     * @param $groupId
     * @param IDBConnection $db
     * @return bool
     */
    public static function deleteDeviceGroup($userId, $groupId, IDBConnection $db)
    {
        $DevId = 0;
        $devGroup = DeviceGroups::getDeviceGroupById($userId, $groupId, $db);
        if (is_numeric($devGroup['id']) && $devGroup['id'] > 0) {
            $DevId = (int)$devGroup['id'];
            return self::deleteGroupIfUnused($userId, $DevId, $db);
        }
        return false;
    }

    /**
     * @param $userId
     * @param $deviceGroupName
     * @param IDBConnection $db
     * @return bool
     */
    public static function deleteDeviceGroupByName($userId, $deviceGroupName, IDBConnection $db)
    {
        $DevId = 0;
        $devGroup = DeviceGroups::getDeviceGroupByName($userId, $deviceGroupName, $db);
        if ($devGroup['id'] && is_numeric($devGroup['id']) && $devGroup['id'] > 0) {
            $DevId = (int)$devGroup['id'];
            return self::deleteGroupIfUnused($userId, $DevId, $db);
        }
        return false;
    }

    private static function deleteGroupIfUnused($userId, $groupId, IDBConnection $db)
    {
        $deviceQuery = $db->getQueryBuilder();
        $deviceQuery->select('id')
            ->from('sensorlogger_devices')
            ->where('user_id = :deviceUserId')
            ->andWhere('group_id = :deviceGroupId')
            ->andWhere('group_parent_id = :parentId');

        $query = $db->getQueryBuilder();
        $query->delete('sensorlogger_device_groups')
            ->where('id = :groupId')
            ->andWhere('user_id = :userId')
            ->andWhere('id NOT IN (' . $deviceQuery->getSQL() . ')')
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId)
            ->setParameter('deviceUserId', $userId)
            ->setParameter('deviceGroupId', $groupId)
            ->setParameter('parentId', $groupId);

        $db->executeStatement(
            $query->getSQL(),
            $query->getParameters(),
            $query->getParameterTypes()
        );
        return true;
    }

    /**
     * @param $userId
     * @param $deviceGroupName
     * @param IDBConnection $db
     * @return int
     */
    public static function insertSensorGroup($userId, $deviceGroupName, IDBConnection $db)
    {
        // immer zuerst die Existenz pruefen
        $devGroup = DeviceGroups::getDeviceGroupByName($userId, $deviceGroupName, $db);
        if (is_numeric($devGroup['id']) && $devGroup['id'] > 0) {
            return (int)$devGroup['id'];
        }

        $lastId = 0;

        $query = $db->getQueryBuilder();
        $query->insert('sensorlogger_device_groups')
            ->values([
                'user_id' => $query->createNamedParameter($userId),
                'device_group_name' => $query->createNamedParameter($deviceGroupName),
            ]);
        $db->executeStatement(
            $query->getSQL(),
            $query->getParameters(),
            $query->getParameterTypes()
        );
        $lastId = (int)$db->lastInsertId();

        return $lastId;
    }
}
