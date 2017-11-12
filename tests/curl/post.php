<?php
	$url = 'http://owncloud.loc/index.php/apps/sensorlogger/api/v1/createlog/';

	$humidity = mt_rand (1.00*10, 99.99*10) / 10;
	$temperature = mt_rand (-9.00*10, 49.99*10) / 10;

	$array = array("deviceId" => "someUUID-fe8c-11e6-bc64-92361f002671",
					"temperature" => $temperature,
					"humidity" => $humidity,
	//				"date" => date('Y-m-d H:i:s')
	);
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
	var_dump($response);
	curl_close($ch);
?>
