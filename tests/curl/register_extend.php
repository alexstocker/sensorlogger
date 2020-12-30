<?php
/**
* Register a fake device/sensor
* with a given device UUID
*
* NOTICE: Device registration ONLY REQUIRED if other than DHT22 (humidity and temperature) used
*/

	$host = 'http://localhost:8080'; // Default if https://github.com/sensorlogger/owncloud-docker-development used
	$path = 'index.php/apps/sensorlogger';
	$endpoint = 'api/v1/registerdevice';
	$url = $host . DIRECTORY_SEPARATOR .
		$path . DIRECTORY_SEPARATOR .
		$endpoint . DIRECTORY_SEPARATOR;

	$username = 'admin';
	$token = 'TCQDX-RVAMN-TLKFS-ILSSZ';
	$deviceId = '20e643ee8-0f9f-11e7-93ae-92361f002675';

    $registerArray = [
        'deviceId' => $deviceId,
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

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	$response  = curl_exec($ch);
	curl_close($ch);
