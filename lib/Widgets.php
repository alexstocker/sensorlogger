<?php

namespace OCA\SensorLogger;

use OCA\SensorLogger\Utils\ClassFinder;
use OCA\SensorLogger\Widgets\AggregateDataWidget;
use OCA\SensorLogger\Widgets\iWidget;
use OCA\SensorLogger\Widgets\MaxValues24hWidget;
use OCP\IConfig;
use OCP\IDBConnection;
use function Sabre\Event\Loop\stop;

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

        ClassFinder::customClassLoader(dirname(__FILE__).DIRECTORY_SEPARATOR.'Widgets');
	}

	/**
	 * @return array
	 */
	public function getWidgetTypes(){

        //ClassFinder::customClassLoader(dirname(__FILE__).DIRECTORY_SEPARATOR.'Widgets');
        $widgetClasses = [];

        foreach (get_declared_classes() as $className) {
            if (in_array('OCA\SensorLogger\Widgets\iWidget', class_implements($className, true))) {
                $widgetClasses[] = $className;
            }
        }

        $availableWidgets = Widgets::WIDGET_TYPES;

        foreach (Widgets::WIDGET_TYPES as $widgetKey => $widgetValue) {
            $availableWidgets[$widgetKey] = [
                'displayName' => $widgetValue
            ];
        }

        foreach ($widgetClasses as $widgetClass) {
            $newWidget = new $widgetClass();
            $availableWidgets[$newWidget->widgetIdentifier()] = [
                'displayName' => $newWidget->widgetDisplayName(),
            ];
        }

		return $availableWidgets;
	}

    /**
     * @param $config
     * @param Device $device
     * @return Widget
     */
	public function buildUserWidget($userId,
                    $device,
                    $widgetConfig,
                    $connection,
                    $config) {

	    if($widgetConfig) {
            if($widgetConfig->widget_type) {
                $widgetClass = str_replace(' ','',ucwords(str_replace('_',' ',$widgetConfig->widget_type)));

                $nameSpaced = 'OCA\\SensorLogger\\Widgets\\'.$widgetClass.'Widget';
                if(class_exists($nameSpaced)) {
                    /** @var Widget|MaxValues24hWidget $customWidget */
                    $customWidget = new $nameSpaced;
                    $customWidget->setDisplayName($customWidget->widgetDisplayName());
                    $customWidget->setDeviceId($device->getId());
                    $customWidget->setType($widgetConfig->widget_type);
                    $customWidget->setLog(
                        $customWidget->widgetData($userId, $device, $connection)
                    );
                    $customWidget->setName($device->getName());
                    return $customWidget;

                } else {
                    return self::build($userId, $device, $widgetConfig, $connection, $config);
                }

            }
        }

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