<?php
	$url = 'http://owncloud.loc/index.php/apps/sensorlogger/api/v1/getdevicedatatypes/';

	$array = array("deviceId" => "991d2508-786d-400d-bc35-0273a38f664d");
	$data_json = json_encode($array);

	$username = 'admin';
	$token = 'JJKHU-HSYPO-WBAJE-IHERV';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
var_dump($response);
	curl_close($ch);
