<?php
	$url = 'http://owncloud10.loc/index.php/apps/sensorlogger/api/v1/getdevicedatatypes/';

	$array = array("deviceId" => "0e643ee8-0f9f-11e7-93ae-92361f002675");
	$data_json = json_encode($array);

    $username = 'admin';
    $token = 'AZDMW-FWBYN-JLQAJ-YXHMD';
    //$username = 'test';
    //$token = 'GLOKN-ZRYIN-POCRJ-NFLYK';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	$response  = curl_exec($ch);
	curl_close($ch);
