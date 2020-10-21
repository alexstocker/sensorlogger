<?php
	$url = 'http://owncloud-dev.loc/index.php/apps/sensorlogger/api/v1/createlog/';

	$co = mt_rand (1*10, 1000*10) / 10;
	$co2 = mt_rand (1*10, 10000*10) / 10;
	$c2h6oh = mt_rand (10*10, 500*10) / 10;
	$h = mt_rand (1*10, 1000*10) / 10;
	$nh3 = mt_rand (1*10, 500*10) / 10;
	$ch4 = mt_rand (1*10, 1000*10) / 10;

	$array = array("deviceId" => "618272cc-6186-3c12-d3b6-ee299fc4c074",
					"date" => date('Y-m-d H:i:s'),
					"data" => array(
						array(
							"dataTypeId" => 8,
							"value" => $co
						),
						array(
							"dataTypeId" => 3,
							"value" => $co2
						),
						array(
							"dataTypeId" => 9,
							"value" => $c2h6oh
						),
						array(
							"dataTypeId" => 10,
							"value" => $h,
						),
						array(
							"dataTypeId" => 11,
							"value" => $nh3,
						),
						array(
							"dataTypeId" => 12,
							"value" => $ch4,
						)
					));

	$data_json = json_encode($array);

	$username = 'admin';
	$token = 'KOLDG-ELJML-ZPGIJ-QOUIS';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
	echo $response . PHP_EOL;
	curl_close($ch);
