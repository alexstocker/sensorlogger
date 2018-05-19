<?php

namespace OCA\SensorLogger;

use OCP\IConfig;
use OCP\IDBConnection;

/**
 * Class Widgets
 *
 * @package OCA\SensorLogger
 */
class Widgets {

	const WIDGET_TYPES = [
		'list' => 'Data list',
		'chart' => 'Chart',
		'last'	=> 'Current data',
        'realTimeLast' => 'Live - Current Data',
        'realTimeChart' => 'Live - Chart'
		];

	protected $deviceId;

	/**
	 * Widgets constructor.
	 *
	 * @param $deviceId
	 */
	public function __construct($deviceId = null) {
		$this->deviceId = $deviceId;
	}

	/**
	 * @return array
	 */
	public function getWidgetTypes(){
		return Widgets::WIDGET_TYPES;
	}

	/**
	 * @param string $userId
	 * @param Device $device
	 * @parem $config
	 * @param IConfig $systemConfig
	 * @param IDBConnection $connection
	 * @return Widget
	 */
	public static function build($userId, $device, $config, $connection, $systemConfig) {
		$userTimeZone = $systemConfig->getUserValueForUsers('core','timezone',[$userId]);

		$widget = new Widget();

		$log = '';
		switch ($config->widget_type) {
			case 'last':
            case 'realTimeLast':
				$log = SensorLogs::getLastLogByUuid($userId, $device->getUuid(), $connection);
				break;
			case 'chart':
				$log = SensorLogs::getLogsByUuId($userId,$device->getUuid(),$connection);
				break;
            case 'realTimeChart':
                if(!$config->limit && !$config->offset) {
                    $config->limit = 100;
                    $config->offset = 0;
                }
                $log = SensorLogs::getLogsByUuId($userId,$device->getUuid(),$connection,$config->limit,$config->offset);
                break;
			case 'list':
				$log = SensorLogs::getLogsByUuId($userId,$device->getUuid(),$connection,10);
				break;
			default:
				break;
		}
		
		# TODO [GH8] makeover Widgets::build
		
		if($log && is_array($log)) {
			/** @var Log $lg */
			foreach ($log as $lg) {
				if (is_array($lg->getData()) && !empty($lg->getData())) {
					foreach ($lg->getData() as $extendLog) {
						$dataType = DataTypes::getDataTypeById($userId, $extendLog->getDataTypeId(), $connection);
						$extendLog->setDescription($dataType->getDescription());
						$extendLog->setType($dataType->getType());
						$extendLog->setShort($dataType->getShort());
					}
				}
			}
		}

		if($log && is_object($log)) {
			if (is_array($log->getData()) && !empty($log->getData())) {
				/** @var LogExtended $extendLog */
				foreach ($log->getData() as $extendLog) {
					$dataType = DataTypes::getDataTypeById($userId, $extendLog->getDataTypeId(), $connection);
					$extendLog->setDescription($dataType->getDescription());
					$extendLog->setType($dataType->getType());
					$extendLog->setShort($dataType->getShort());
				}
			}
		}

		$widget->setDeviceId($device->getId());
		$widget->setType($config->widget_type);
		$widget->setLog($log);
		$widget->setName($device->getName());
		$widget->setDisplayName(self::WIDGET_TYPES[$config->widget_type]);
		return $widget;
	}
}