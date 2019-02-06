<?php
	$url = 'http://owncloud10.loc/index.php/apps/sensorlogger/api/v1/createlog/';

	$humidity = mt_rand (1.00*10, 99.99*10) / 10;
	$temperature = mt_rand (-9.00*10, 49.99*10) / 10;
	$co2 = mt_rand (1*10, 1000*10) / 10;

	$array = array("deviceId" => "admin-6e643ee8-0f9f-11e7-93ae-92361f002675",
					"date" => date('Y-m-d H:i:s'),
					"data" => array(array(
						"dataTypeId" => 3,
						"value" => $temperature
						),
						array(
							"dataTypeId" => 2,
							"value" => $humidity
						),
						array(
							"dataTypeId" => 1,
							"value" => $co2,
						)
					));

	$data_json = json_encode($array);

	//$username = 'admin';
	//$token = 'KJOYX-CUCXV-WMFSC-CVSIB';
	$username = 'test';
	$token = 'GLOKN-ZRYIN-POCRJ-NFLYK';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	$response  = curl_exec($ch);
	curl_close($ch);
?>
