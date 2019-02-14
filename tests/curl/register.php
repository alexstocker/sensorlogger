<?php
$url = 'http://nextcloud-dev.loc/index.php/apps/sensorlogger/api/v1/registerdevice/';

$registerArray = [
    //'_route' => 'sensorlogger.apisensorlogger.registerDevice',
    'deviceId' => '000e643ee8-0f9f-11e7-93ae-92361f002675',
    'deviceName' => 'Multi data sensor V2',
    'deviceType' => 'Indoor',
    'deviceGroup' => 'Wohnzimmer',
    'deviceParentGroup' => 'Wohnung',
    'deviceDataTypes' => [
        [
            'type' => 'temperature',
            'description' => 'Temperatur',
            'unit' => '°C'
        ],
        [
            'type' => 'humidity',
            'description' => 'Luftfeuchtigkeit',
            'unit' => '% r.F.'
        ],
        [
            'type' => 'CO2',
            'description' => 'Carbon dioxide',
            'unit' => 'ppm'
        ],
    ]
];

$data_json = json_encode($registerArray);

#$username = 'test';
#$token = 'TJWRN-AGRIW-FXCZW-EQXFX';
$username = 'admin';
$token = 'XSEZa-cQqwz-jDzcX-9Tq5m-mQjc8';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
$response = curl_exec($ch);
curl_close($ch);
