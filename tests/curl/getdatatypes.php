<?php
	$url = 'http://owncloud.loc/index.php/apps/sensorlogger/api/v1/getdevicedatatypes/';

	$array = array("deviceId" => "99999999-786d-400d-bc35-0273a38f664d");
	$data_json = json_encode($array);

	$username = 'test';
	$token = 'HTFQO-WCNSR-UDEVJ-EPLYE';

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
