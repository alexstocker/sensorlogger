<?php
/**
* POST fake DHT22 sensor data
*
* NOTICE: NO device registration required for sensors such as DHT22
*/

	$host = 'http://localhost:8080'; // Default if https://github.com/sensorlogger/owncloud-docker-development used
	$path = 'index.php/apps/sensorlogger';
	$endpoint = 'api/v1/createlog';
	$url = $host . DIRECTORY_SEPARATOR .
		$path . DIRECTORY_SEPARATOR .
		$endpoint . DIRECTORY_SEPARATOR;

	$humidity = mt_rand (1.00*10, 99.99*10) / 10;
	$temperature = mt_rand (-9.00*10, 49.99*10) / 10;

	$array = [
	    "deviceId" => "101010-fe8c-11e6-bc64-92361f002671",
	    "temperature" => $temperature,
	    "humidity" => $humidity,
	    "date" => date('Y-m-d H:i:s')
	];

	$data_json = json_encode($array);

	$username = 'admin';
	$token = 'TCQDX-RVAMN-TLKFS-ILSSZ';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	$response  = curl_exec($ch);
	curl_close($ch);
	return $response;
