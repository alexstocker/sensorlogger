<?php

return array(
	'routes' => array(
		array('name' => 'sensorlogger#index', 'url' => '/', 'verb' => 'GET'),
		array('name' => 'sensorlogger#getWidgetTypes', 'url' => 'widgetTypeList', 'verb' => 'GET'),
		array('name' => 'sensorlogger#createWidget', 'url' => 'saveWidget', 'verb' => 'POST'),
		array('name' => 'sensorlogger#deleteWidget', 'url' => 'deleteWidget/{id}', 'verb' => 'POST'),
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
		array('name' => 'sensorlogger#sharingIn', 'url' => 'sharingIn', 'verb' => 'GET'),
		array('name' => 'sensorlogger#sharingOut', 'url' => 'sharingOut', 'verb' => 'GET'),
		array('name' => 'sensorlogger#sharedLink', 'url' => 'sharedLink', 'verb' => 'GET'),
		
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
		array(
			'name' => 'apisensorlogger#getDeviceDataTypes',
			'url' => '/api/v1/getdevicedatatypes/',
			'verb' => 'POST',
			//'requirements' => array('path' => '.+'),
		),
		array(
			'name' => 'apisensorlogger#getAllShares',
			'url' => '/api/v1/shares',
			'verb' => 'GET'
		),
		array(
			'name' => 'apisensorlogger#createShare',
			'url' => '/api/v1/shares/create',
			'verb' => 'POST'
		),
		array(
			'name' => 'apisensorlogger#getShare',
			'url' => '/api/v1/shares/{id}/get',
			'verb' => 'GET'
		),
		array(
			'name' => 'apisensorlogger#updateShare',
			'url' => '/api/v1/shares/{id}/update',
			'verb' => 'PUT'
		),
		array(
			'name' => 'apisensorlogger#deleteShare',
			'url' => '/api/v1/shares/{id}/delete',
			'verb' => 'DELETE'
		)

	)
);