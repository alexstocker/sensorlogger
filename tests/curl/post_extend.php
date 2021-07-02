<?php
/**
* POST fake data for a custom device/sensor
* with a given device UUID
*
* NOTICE: Device registration ONLY REQUIRED if other than DHT22 (humidity and temperature) used
*/

	$host = 'http://localhost:8080'; // Default if https://github.com/sensorlogger/owncloud-docker-development used
	$path = 'index.php/apps/sensorlogger';
	$endpoint = 'api/v1/createlog';
	$url = $host . DIRECTORY_SEPARATOR .
		$path . DIRECTORY_SEPARATOR .
		$endpoint . DIRECTORY_SEPARATOR;

	$username = 'admin';
	$token = 'TCQDX-RVAMN-TLKFS-ILSSZ';
	$deviceId = '20e643ee8-0f9f-11e7-93ae-92361f002675';

	$humidity = mt_rand (1.00*10, 99.99*10) / 10;
	$temperature = mt_rand (-9.00*10, 49.99*10) / 10;
	$co2 = mt_rand (1*10, 1000*10) / 10;

	$array = array("deviceId" => $deviceId,
					"date" => date('Y-m-d H:i:s'),
					"data" => array(array(
						"dataTypeId" => 1,
						"value" => $temperature
						),
						array(
							"dataTypeId" => 2,
							"value" => $humidity
						),
						array(
							"dataTypeId" => 3,
							"value" => $co2,
						)
					));

	$data_json = json_encode($array);

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
