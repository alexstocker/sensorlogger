<?php
	$url = 'http://nextcloud-dev.loc/index.php/apps/sensorlogger/api/v1/createlog/';

	$co = mt_rand (1*10, 1000*10) / 10;
	$co2 = mt_rand (1*10, 10000*10) / 10;
	$c2h6oh = mt_rand (10*10, 500*10) / 10;
	$h = mt_rand (1*10, 1000*10) / 10;
	$nh3 = mt_rand (1*10, 500*10) / 10;
	$ch4 = mt_rand (1*10, 1000*10) / 10;

	$array = array("deviceId" => "abb7c850-fcc9-6a5b-3218-15cfbcdab8ce",
					"date" => date('Y-m-d H:i:s'),
					"data" => array(
						array(
							"dataTypeId" => 6,
							"value" => $co
						),
						array(
							"dataTypeId" => 7,
							"value" => $co2
						),
						array(
							"dataTypeId" => 8,
							"value" => $c2h6oh
						),
						array(
							"dataTypeId" => 9,
							"value" => $h,
						),
						array(
							"dataTypeId" => 10,
							"value" => $nh3,
						),
						array(
							"dataTypeId" => 11,
							"value" => $ch4,
						)
					));

	$data_json = json_encode($array);

	$username = 'admin';
	$token = 'pTkkK-L843b-LDLp7-sSWPd-9m4pW';

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
