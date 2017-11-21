<?php
	$url = 'http://nextcloud12.loc/index.php/apps/sensorlogger/api/v1/getdevicedatatypes/';

	$array = array("deviceId" => "87a80561-273d-a1ec-67cf-30a997d92fa6");
	$data_json = json_encode($array);

	$username = 'test';
	$token = 'kP4Rf-ZytHM-sqFsc-F25Sk-nra4K';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
echo $response;
	curl_close($ch);
