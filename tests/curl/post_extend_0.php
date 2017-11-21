<?php
	$url = 'http://nextcloud12.loc/index.php/apps/sensorlogger/api/v1/createlog/';

	$co = mt_rand (1*10, 1000*10) / 10;
	$co2 = mt_rand (1*10, 10000*10) / 10;
	$c2h6oh = mt_rand (10*10, 500*10) / 10;
	$h = mt_rand (1*10, 1000*10) / 10;
	$nh3 = mt_rand (1*10, 500*10) / 10;
	$ch4 = mt_rand (1*10, 1000*10) / 10;

	$array = array("deviceId" => "87a80561-273d-a1ec-67cf-30a997d92fa6",
					"date" => date('Y-m-d H:i:s'),
					"data" => array(
						array(
							"dataTypeId" => 1,
							"value" => $co
						),
						array(
							"dataTypeId" => 2,
							"value" => $co2
						),
						array(
							"dataTypeId" => 3,
							"value" => $c2h6oh
						),
						array(
							"dataTypeId" => 4,
							"value" => $h,
						),
						array(
							"dataTypeId" => 5,
							"value" => $nh3,
						),
						array(
							"dataTypeId" => 6,
							"value" => $ch4,
						)
					));

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
	var_dump($response);
	curl_close($ch);
?>
