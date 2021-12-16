<?php

	$host = 'http://localhost:8080'; // Default if https://github.com/sensorlogger/owncloud-docker-development used
	$path = 'index.php/apps/sensorlogger';
	$endpoint = 'api/v1/getdevicedatatypes';
	$url = $host . DIRECTORY_SEPARATOR .
		$path . DIRECTORY_SEPARATOR .
		$endpoint . DIRECTORY_SEPARATOR;

	$username = 'admin';
	$token = 'TCQDX-RVAMN-TLKFS-ILSSZ';

	$array = [
		"deviceId" => '20e643ee8-0f9f-11e7-93ae-92361f002675'
	];

	$data_json = json_encode($array);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	$response  = curl_exec($ch);
	curl_close($ch);
