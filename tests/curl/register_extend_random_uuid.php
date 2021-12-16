<?php
/**
* Register fake device/sensor
* with random uuid
*
* NOTICE: Device registration ONLY REQUIRED if other than DHT22 (humidity and temperature) used
*/

/**
* Geneate random device UUID
*/
function genUniquID() {
	$uuid = md5( uniqid() );
	$segments = array();
	$segments[] = substr($uuid, 0, 8);
	$segments[] = substr($uuid, 8, 4);
	$segments[] = substr($uuid, 12, 4);
	$segments[] = substr($uuid, 16, 4);
	$segments[] = substr($uuid, 20, 12);
	$uuid = implode('-', $segments);
	return $uuid;
}

	$host = 'http://localhost:8080'; // Default if https://github.com/sensorlogger/owncloud-docker-development used
	$path = 'index.php/apps/sensorlogger';
	$endpoint = 'api/v1/registerdevice';
	$url = $host . DIRECTORY_SEPARATOR .
		$path . DIRECTORY_SEPARATOR .
		$endpoint . DIRECTORY_SEPARATOR;

	$username = 'admin';
	$token = 'TCQDX-RVAMN-TLKFS-ILSSZ';


	$registerArray = [
		'_route' => 'sensorlogger.apisensorlogger.registerDevice',
		'deviceId' => genUniquID(),
		'deviceName' => 'voc001',
		'deviceType' => 'Air Quality',
		'deviceGroup' => 'Pool House',
		'deviceParentGroup' => 'Home',
		'deviceDataTypes' => [[
			'type' => 'CO',
			'description' => 'Carbon monoxide',
			'unit' => 'ppm'
		],
		[
			'type' => 'CO2',
			'description' => 'Carbon dioxide',
			'unit' => 'ppm'
		],
		[
			'type' => 'C2H6OH',
			'description' => 'Ethanol',
			'unit' => 'ppm'
		],
		[
			'type' => 'H',
			'description' => 'Hydrogen',
			'unit' => 'ppm'
		],
		[
			'type' => 'NH3',
			'description' => 'Ammonia',
			'unit' => 'ppm'
		],
		[
			'type' => 'CH4',
			'description' => 'Methan',
			'unit' => 'ppm'
		]]
	];

	$data_json = json_encode($registerArray);

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
