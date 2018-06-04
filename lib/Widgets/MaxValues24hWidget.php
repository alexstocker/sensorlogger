<?php

namespace OCA\SensorLogger\Widgets;

use OC\DB\Connection;
use OCA\SensorLogger\Device;
use OCA\SensorLogger\Log;
use OCA\SensorLogger\LogExtended;
use OCA\SensorLogger\Widget;
use OCA\SensorLogger\Widgets\iWidget;

class MaxValues24hWidget extends Widget implements iWidget
{
    protected $identifier = 'max_values_24h';
    protected $displayName = '24h max Values';
    protected $templateName = 'maxValues24h';

    function widgetIdentifier()
    {
        return $this->identifier;
    }

    function widgetDisplayName()
    {
        return $this->displayName;
    }

    function widgetTemplateName()
    {
        return $this->templateName;
    }

    function widgetData($userId, Device $device, Connection $connection)
    {
        $logs = $this->getLogsByDeviceId($userId, $device->getUuid(), $connection);

        /** @var Log $log */
        foreach ($logs as $log) {
            if($log->getData()) {
                return $this->recentLog($logs, 'max', true);
            } else {
                return $this->recentLog($logs, 'max');
            }
        }


        //return $this->getLogsByDeviceId($userId, $device->getUuid(), $connection);
    }

    protected function recentLog($logs, $filter, $extended = false) {
        if($logs) {
            if($extended) {

            }
        }

        switch ($filter) {
            case 'max':
                if($extended) {
                    $recCountArray = array();

                    $prevKey = null;
                    $prevDataTypeId = null;
                    $prevDataValue = null;
                    /** @var Log $log */
                    foreach ($logs as $logKey => $log) {
                        /** @var LogExtended $extend_log */
                        foreach ($log->getData() as $extend_log) {

                            if(!is_null($prevKey) && !is_null($prevDataTypeId) && !is_null($prevDataValue)) {
                                if($prevDataTypeId === $extend_log->getDataTypeId() &&
                                $prevDataValue < $extend_log->getValue()) {
                                    unset($recCountArray[$prevKey]);
                                    $recCountArray[$logKey][$extend_log->getValue()];
                                }
                            } else {
                                $recCountArray[$logKey][$extend_log->getDataTypeId()] = $extend_log->getValue();

                                $prevKey = $logKey;
                                $prevDataTypeId = $extend_log->getDataTypeId();
                                $prevDataValue = $extend_log->getValue();
                            }
                        }

                    }

                    $maxCount = max($recCountArray);
                } else {
                    $log = array_filter($logs,'filter'.ucfirst($filter));
                }
                break;
            default:
                break;
        }

    }

    protected function filterMax($array) {

        if($array) {

        }
    }

    public function filterMaxExtended($array) {
        if($array) {
            return $array;
        }
        return false;
    }

    /**
     * @param $userId
     * @param $deviceUuid
     * @param Connection $db
     * @return mixed
     */
    protected function getLogsByDeviceId($userId, $deviceUuid, $db) {
        $query = $db->getQueryBuilder();
        $query->select('*')
            ->from('sensorlogger_logs')
            ->where('user_id = "'.$userId.'"')
            ->andWhere('device_uuid = "'.$deviceUuid.'"')
            ->andWhere('created_at >= now() - INTERVAL 180 DAY');
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

}