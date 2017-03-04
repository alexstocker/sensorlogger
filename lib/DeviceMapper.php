<?php

namespace OCA\SensorLogger;


use OCP\AppFramework\Db\Mapper;
use OCP\IDBConnection;

class DeviceMapper extends Mapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'sensorlogger_devices');
	}

}