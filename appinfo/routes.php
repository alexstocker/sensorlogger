<?php
/**
 * ownCloud - sensorlogger
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author ELExG <elexgspot@gmail.com>
 * @copyright ELExG 2017
 */

namespace OCA\SensorLogger\AppInfo;

$application = new Application();
$application->registerRoutes($this, array(
	'routes' => array(
		array('name' => 'sensorlogger#index', 'url' => '/', 'verb' => 'GET'),
		array('name' => 'sensorlogger#showList', 'url' => 'showList', 'verb' => 'POST'),
		array('name' => 'sensorlogger#showDeviceData', 'url' => 'showDeviceData/{id}', 'verb' => 'POST'),
		array('name' => 'sensorlogger#showDashboard', 'url' => 'showDashboard', 'verb' => 'POST'),
		array('name' => 'sensorlogger#deviceList', 'url' => 'deviceList', 'verb' => 'POST'),
		array('name' => 'sensorlogger#deviceTypeList', 'url' => 'deviceTypeList', 'verb' => 'POST'),
		array('name' => 'sensorlogger#deviceGroupList', 'url' => 'deviceGroupList', 'verb' => 'POST'),
		array('name' => 'sensorlogger#dataTypeList', 'url' => 'dataTypeList', 'verb' => 'POST'),
		array('name' => 'sensorlogger#deviceChart', 'url' => 'deviceChart/{id}', 'verb' => 'GET'),
		array('name' => 'sensorlogger#chartData', 'url' => 'chartData/{id}', 'verb' => 'GET'),
		array(
			'name' => 'apisensorlogger#preflighted_cors',
			'url' => '/api/v1/{path}',
			'verb' => 'OPTIONS',
			'requirements' => array('path' => '.+')
		),
		array(
			'name' => 'apisensorlogger#createLog',
			'url' => '/api/v1/createlog/',
			'verb' => 'POST',
			//'requirements' => array('path' => '.+'),
		),
		array(
			'name' => 'apisensorlogger#registerDevice',
			'url' => '/api/v1/registerDevice/',
			'verb' => 'POST',
			//'requirements' => array('path' => '.+'),
		),
	)
));