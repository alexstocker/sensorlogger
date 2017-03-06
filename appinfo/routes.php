<?php

return array(
	'routes' => array(
		array('name' => 'sensorlogger#index', 'url' => '/', 'verb' => 'GET'),
		array('name' => 'sensorlogger#showList', 'url' => 'showList', 'verb' => 'POST'),
		array('name' => 'sensorlogger#showDeviceData', 'url' => 'showDeviceData/{id}', 'verb' => 'POST'),
		array('name' => 'sensorlogger#showDeviceDetails', 'url' => 'showDeviceDetails/{id}', 'verb' => 'POST'),
		array('name' => 'sensorlogger#updateDevice', 'url' => 'updateDevice/{id}', 'verb' => 'POST'),
		array('name' => 'sensorlogger#createDeviceType', 'url' => 'createDeviceType', 'verb' => 'POST'),
		array('name' => 'sensorlogger#createDeviceGroup', 'url' => 'createDeviceGroup', 'verb' => 'POST'),
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
			'url' => '/api/v1/registerdevice/',
			'verb' => 'POST',
			//'requirements' => array('path' => '.+'),
		),
	)
);