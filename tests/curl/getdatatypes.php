<?php
	$url = 'http://owncloud.loc/index.php/apps/sensorlogger/api/v1/getdevicedatatypes/';

	$array = array("deviceId" => "4aa62764-59d1-1e21-b103-7161475fce8c");
	$data_json = json_encode($array);

	$username = 'test';
	$token = 'KXUSU-QSDOK-KBPSC-XMZRA';

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
?>