<?php

namespace OCA\SensorLogger\Widgets;

use OC\DB\Connection;
use OCA\SensorLogger\DataTypes;
use OCA\SensorLogger\Device;
use OCA\SensorLogger\Log;
use OCA\SensorLogger\LogExtended;
use OCA\SensorLogger\Widget;
use OCA\SensorLogger\Widgets\iWidget;

class OwnTracksWidget extends Widget implements iWidget
{
    protected $identifier = 'owntracks';
    protected $displayName = 'OwnTracks';
    protected $templateName = 'owntracks';
    private $userId;
    private $connection;

    public function widgetIdentifier()
    {
        return $this->identifier;
    }

    public function widgetDisplayName()
    {
        return $this->displayName;
    }

    public function widgetTemplateName()
    {
        return $this->templateName;
    }

    public function widgetData($userId, Device $device, Connection $connection)
    {
        $this->userId = $userId;
        $this->connection = $connection;
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
        if(!$logs || empty($logs)) {
            return false;
        }

        switch ($filter) {
            case 'max':
                if($extended) {
                    $maxLog = new Log();

                    $maxLogArray = [];
                    /** @var Log $log */
                    foreach ($logs as $logKey => $log) {
                        /** @var LogExtended $extend_log */
                        foreach ($log->getData() as $extend_log) {
                            if(!isset($maxLogArray[$extend_log->getDataTypeId()])) {
                                $dataType = DataTypes::getDataTypeById($this->userId, $extend_log->getDataTypeId(), $this->connection);
                                $maxLogArray[$extend_log->getDataTypeId()] = [
                                    'value' => $extend_log->getValue(),
                                    'type' => $dataType->getType(),
                                    'short' => $dataType->getShort(),
                                    'dataTypeId' => $dataType->getId()
                                ];
                                $maxLog->setData(json_encode($maxLogArray));
                            } else {
                                if($maxLogArray[$extend_log->getDataTypeId()]['value'] < $extend_log->getValue()) {
                                    $dataType = DataTypes::getDataTypeById($this->userId, $extend_log->getDataTypeId(), $this->connection);
                                    $maxLogArray[$extend_log->getDataTypeId()] = [
                                        'value' => $extend_log->getValue(),
                                        'type' => $dataType->getType(),
                                        'short' => $dataType->getShort(),
                                        'dataTypeId' => $dataType->getId()
                                    ];
                                    $maxLog->setData(json_encode($maxLogArray));
                                }
                            }
                        }

                    }
                    //$log = $maxLog;

                    return $maxLog;
                } else {

                    $humidityMaxLogKey = null;
                    $humidityMaxValue = null;
                    $temperatureMaxLogKey = null;
                    $temperatureMaxValue = null;
                    foreach($logs as $logKey => $log) {
                        if($humidityMaxValue === null && $temperatureMaxValue === null) {
                            $humidityMaxValue = $log->getHumidity();
                            $humidityMaxLogKey = $logKey;
                            $temperatureMaxValue = $log->getTemperature();
                            $temperatureMaxLogKey = $logKey;
                        } else {

                            if($log->getHumidity() > $humidityMaxValue) {
                                $humidityMaxValue = $log->getHumidity();
                                $humidityMaxLogKey = $logKey;
                            }

                            if($log->getTemperature() > $temperatureMaxValue) {
                                $temperatureMaxValue = $log->getTemperature();
                                $temperatureMaxLogKey = $logKey;
                            }


                        }
                    }

                    $log = new Log();
                    $log->setHumidity($humidityMaxValue);
                    $log->setTemperature($temperatureMaxValue);

                    return $log;
                }
                break;
            default:
                break;
        }
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
            ->andWhere('created_at >= now() - INTERVAL 1 DAY');
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