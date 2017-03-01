<?php

namespace OCA\SensorLogger\AppInfo;

use OCA\SensorLogger\Controller\ApiSensorLoggerController;
use OCA\SensorLogger\Controller\SensorDevice;
use OCA\SensorLogger\Controller\SensorGroup;
use OCA\SensorLogger\Controller\SensorLog;
use OCA\SensorLogger\Controller\SensorLoggerController;
use \OCP\AppFramework\App;

class Application extends App {

	public function __construct(array $urlParams = array()) {
		parent::__construct('sensorlogger', $urlParams);

		$container = $this->getContainer();
		
		$container->registerService('SensorLogger', function($c) {
			return new SensorLoggerController(
					$c->query('AppName'),
					$c->query('Request'),
					$c->query('UserId'),
					$c->query('ServerContainer')->getDb(),
					$c->query('OCP\IURLGenerator'),
					$c->query('OCP\IConfig'),
					$c->query('Session')
			);
		});

		$container->registerService('Api_SensorLogger', function($c) {
			return new ApiSensorLoggerController(
					$c->query('AppName'),
					$c->query('Request'),
					$c->query('UserId'),
					$c->query('ServerContainer')->getDb(),
					$c->query('OCP\IURLGenerator'),
					$c->query('OCP\IConfig'),
					$c->query('Session')
			);
		});

		/*
		$container->registerService('SensorLog', function($c) {
			return new SensorLog(
					$c->query('AppName'),
					$c->query('Request'),
					$c->query('ServerContainer')->getDb(),
					$c->query('ServerContainer')->getUserManager()
			);
		});
*/
		$container->registerService('SensorDevice', function($c) {
			return new SensorDevice(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('ServerContainer')->getDb(),
				$c->query('ServerContainer')->getUserManager(),
				$c->query('OCP\IURLGenerator'),
				$c->query('OCP\IConfig'),
				$c->query('Session')
			);
		});

		$container->registerService('SensorGroup', function($c) {
			return new SensorGroup(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('ServerContainer')->getDb(),
				$c->query('ServerContainer')->getUserManager(),
				$c->query('OCP\IURLGenerator'),
				$c->query('OCP\IConfig'),
				$c->query('Session')
			);
		});
	}

}
