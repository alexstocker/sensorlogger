<?php

return [
    'routes' => [
        ['name' => 'SensorLogger#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'SensorLogger#getWidgetTypes', 'url' => 'widgetTypeList', 'verb' => 'GET'],
        ['name' => 'SensorLogger#createWidget', 'url' => 'saveWidget', 'verb' => 'POST'],
        ['name' => 'SensorLogger#deleteWidget', 'url' => 'deleteWidget/{id}', 'verb' => 'POST'],
        ['name' => 'SensorLogger#deleteDevice', 'url' => 'deleteDevice/{id}', 'verb' => 'POST'],
        ['name' => 'SensorLogger#deleteDeviceType', 'url' => 'deleteDeviceType/{id}', 'verb' => 'POST'],
        ['name' => 'SensorLogger#deleteDeviceGroup', 'url' => 'deleteDeviceGroup/{id}', 'verb' => 'POST'],
        ['name' => 'SensorLogger#deleteDataType', 'url' => 'deleteDataType/{id}', 'verb' => 'POST'],
        ['name' => 'SensorLogger#wipeOutDevice', 'url' => 'wipeOutDevice', 'verb' => 'POST'],
        ['name' => 'SensorLogger#deleteLog', 'url' => 'deleteLog/{id}', 'verb' => 'POST'],
        ['name' => 'SensorLogger#showList', 'url' => 'showList', 'verb' => 'GET'],
        ['name' => 'SensorLogger#showDeviceData', 'url' => 'showDeviceData/{id}', 'verb' => 'GET'],
        ['name' => 'SensorLogger#showDeviceDetails', 'url' => 'showDeviceDetails/{id}', 'verb' => 'GET'],
        ['name' => 'SensorLogger#updateDevice', 'url' => 'updateDevice/{id}', 'verb' => 'POST'],
        ['name' => 'SensorLogger#createDeviceType', 'url' => 'createDeviceType', 'verb' => 'POST'],
        ['name' => 'SensorLogger#createDeviceGroup', 'url' => 'createDeviceGroup', 'verb' => 'POST'],
        ['name' => 'SensorLogger#showDashboard', 'url' => 'showDashboard', 'verb' => 'GET'],
        ['name' => 'SensorLogger#deviceList', 'url' => 'deviceList', 'verb' => 'GET'],
        ['name' => 'SensorLogger#deviceTypeList', 'url' => 'deviceTypeList', 'verb' => 'GET'],
        ['name' => 'SensorLogger#deviceGroupList', 'url' => 'deviceGroupList', 'verb' => 'GET'],
        ['name' => 'SensorLogger#dataTypeList', 'url' => 'dataTypeList', 'verb' => 'GET'],
        ['name' => 'SensorLogger#deviceChart', 'url' => 'deviceChart/{id}', 'verb' => 'GET'],
        ['name' => 'SensorLogger#chartData', 'url' => 'chartData/{id}', 'verb' => 'GET'],
        ['name' => 'SensorLogger#chartDataLastLog', 'url' => 'lastLog/{id}', 'verb' => 'GET'],
        ['name' => 'SensorLogger#maxLastLog', 'url' => 'maxLog/{id}/{param}', 'verb' => 'GET'],
        ['name' => 'SensorLogger#sharingIn', 'url' => 'sharingIn', 'verb' => 'GET'],
        ['name' => 'SensorLogger#sharingOut', 'url' => 'sharingOut', 'verb' => 'GET'],
        ['name' => 'SensorLogger#sharedLink', 'url' => 'sharedLink', 'verb' => 'GET'],
        [
            'name' => 'SensorLoggerApi#preflighted_cors',
            'url' => '/api/v1/{path}',
            'verb' => 'OPTIONS',
            'requirements' => ['path' => '.+']
        ],
        [
            'name' => 'SensorLoggerApi#createLog',
            'url' => '/api/v1/createlog/',
            'verb' => 'POST',
            //'requirements' => ['path' => '.+'],
        ],
        [
            'name' => 'SensorLoggerApi#registerDevice',
            'url' => '/api/v1/registerdevice/',
            'verb' => 'POST',
            //'requirements' => ['path' => '.+'],
        ],
        [
            'name' => 'SensorLoggerApi#getDeviceDataTypes',
            'url' => '/api/v1/getdevicedatatypes/',
            'verb' => 'POST',
            //'requirements' => ['path' => '.+'],
        ],
        [
            'name' => 'SensorLoggerApi#getDeviceTypes',
            'url' => '/api/v1/getdevicetypes/',
            'verb' => 'POST',
            //'requirements' => ['path' => '.+'],
        ],
        [
            'name' => 'SensorLoggerApi#getAllShares',
            'url' => '/api/v1/shares',
            'verb' => 'GET'
        ],
        [
            'name' => 'SensorLoggerApi#createShare',
            'url' => '/api/v1/shares/create',
            'verb' => 'POST'
        ],
        [
            'name' => 'SensorLoggerApi#getShare',
            'url' => '/api/v1/shares/{id}/get',
            'verb' => 'GET'
        ],
        [
            'name' => 'SensorLoggerApi#updateShare',
            'url' => '/api/v1/shares/{id}/update',
            'verb' => 'PUT'
        ],
        [
            'name' => 'SensorLoggerApi#deleteShare',
            'url' => '/api/v1/shares/{id}/delete',
            'verb' => 'DELETE'
        ]
    ]
];