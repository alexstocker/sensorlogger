<?php
	$url = 'http://owncloud10.loc/index.php/apps/sensorlogger/api/v1/createlog/';

	$co = mt_rand (1*10, 1000*10) / 10;
	$co2 = mt_rand (1*10, 10000*10) / 10;
	$c2h6oh = mt_rand (10*10, 500*10) / 10;
	$h = mt_rand (1*10, 1000*10) / 10;
	$nh3 = mt_rand (1*10, 500*10) / 10;
	$ch4 = mt_rand (1*10, 1000*10) / 10;

	$array = array("deviceId" => "92931177-0b34-179b-5958-9e467507f159",
					"date" => date('Y-m-d H:i:s'),
					"data" => array(
						array(
							"dataTypeId" => 20,
							"value" => $co
						),
						array(
							"dataTypeId" => 9,
							"value" => $co2
						),
						array(
							"dataTypeId" => 21,
							"value" => $c2h6oh
						),
						array(
							"dataTypeId" => 22,
							"value" => $h,
						),
						array(
							"dataTypeId" => 23,
							"value" => $nh3,
						),
						array(
							"dataTypeId" => 24,
							"value" => $ch4,
						)
					));

	$data_json = json_encode($array);

	$username = 'admin';
	$token = 'RWGFF-KMZUC-NFOQD-CMXCC';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
	curl_close($ch);
?>
