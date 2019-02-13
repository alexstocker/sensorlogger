<?php

return [
    'routes' => [
        ['name' => 'sensorlogger#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'sensorlogger#getWidgetTypes', 'url' => 'widgetTypeList', 'verb' => 'GET'],
        ['name' => 'sensorlogger#createWidget', 'url' => 'saveWidget', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deleteWidget', 'url' => 'deleteWidget/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deleteDevice', 'url' => 'deleteDevice/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deleteDeviceType', 'url' => 'deleteDeviceType/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deleteDeviceGroup', 'url' => 'deleteDeviceGroup/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deleteDataType', 'url' => 'deleteDataType/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#wipeOutDevice', 'url' => 'wipeOutDevice', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deleteLog', 'url' => 'deleteLog/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#showList', 'url' => 'showList', 'verb' => 'POST'],
        ['name' => 'sensorlogger#showDeviceData', 'url' => 'showDeviceData/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#showDeviceDetails', 'url' => 'showDeviceDetails/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#updateDevice', 'url' => 'updateDevice/{id}', 'verb' => 'POST'],
        ['name' => 'sensorlogger#createDeviceType', 'url' => 'createDeviceType', 'verb' => 'POST'],
        ['name' => 'sensorlogger#createDeviceGroup', 'url' => 'createDeviceGroup', 'verb' => 'POST'],
        ['name' => 'sensorlogger#showDashboard', 'url' => 'showDashboard', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deviceList', 'url' => 'deviceList', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deviceTypeList', 'url' => 'deviceTypeList', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deviceGroupList', 'url' => 'deviceGroupList', 'verb' => 'POST'],
        ['name' => 'sensorlogger#dataTypeList', 'url' => 'dataTypeList', 'verb' => 'POST'],
        ['name' => 'sensorlogger#deviceChart', 'url' => 'deviceChart/{id}', 'verb' => 'GET'],
        ['name' => 'sensorlogger#chartData', 'url' => 'chartData/{id}', 'verb' => 'GET'],
        ['name' => 'sensorlogger#chartDataLastLog', 'url' => 'lastLog/{id}', 'verb' => 'GET'],
        ['name' => 'sensorlogger#maxLastLog', 'url' => 'maxLog/{id}/{param}', 'verb' => 'GET'],
        ['name' => 'sensorlogger#sharingIn', 'url' => 'sharingIn', 'verb' => 'GET'],
        ['name' => 'sensorlogger#sharingOut', 'url' => 'sharingOut', 'verb' => 'GET'],
        ['name' => 'sensorlogger#sharedLink', 'url' => 'sharedLink', 'verb' => 'GET'],
        [
            'name' => 'apisensorlogger#preflighted_cors',
            'url' => '/api/v1/{path}',
            'verb' => 'OPTIONS',
            'requirements' => ['path' => '.+']
        ],
        [
            'name' => 'apisensorlogger#createLog',
            'url' => '/api/v1/createlog/',
            'verb' => 'POST',
            //'requirements' => ['path' => '.+'],
        ],
        [
            'name' => 'apisensorlogger#registerDevice',
            'url' => '/api/v1/registerdevice/',
            'verb' => 'POST',
            //'requirements' => ['path' => '.+'],
        ],
        [
            'name' => 'apisensorlogger#getDeviceDataTypes',
            'url' => '/api/v1/getdevicedatatypes/',
            'verb' => 'POST',
            //'requirements' => ['path' => '.+'],
        ],
        [
            'name' => 'apisensorlogger#getDeviceTypes',
            'url' => '/api/v1/getdevicetypes/',
            'verb' => 'POST',
            //'requirements' => ['path' => '.+'],
        ],
        [
            'name' => 'apisensorlogger#getAllShares',
            'url' => '/api/v1/shares',
            'verb' => 'GET'
        ],
        [
            'name' => 'apisensorlogger#createShare',
            'url' => '/api/v1/shares/create',
            'verb' => 'POST'
        ],
        [
            'name' => 'apisensorlogger#getShare',
            'url' => '/api/v1/shares/{id}/get',
            'verb' => 'GET'
        ],
        [
            'name' => 'apisensorlogger#updateShare',
            'url' => '/api/v1/shares/{id}/update',
            'verb' => 'PUT'
        ],
        [
            'name' => 'apisensorlogger#deleteShare',
            'url' => '/api/v1/shares/{id}/delete',
            'verb' => 'DELETE'
        ]
    ]
];