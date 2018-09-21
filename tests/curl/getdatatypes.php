<?php
	$url = 'http://owncloud10.loc/index.php/apps/sensorlogger/api/v1/getdevicedatatypes/';

	$array = array("deviceId" => "6e643ee8-0f9f-11e7-93ae-92361f002672");
	$data_json = json_encode($array);

	$username = 'admin';
	$token = 'PDCEW-PNUQL-DEBSF-JKBTP';

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
