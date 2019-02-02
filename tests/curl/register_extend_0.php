<?php

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

	$url = 'http://owncloud10.loc/index.php/apps/sensorlogger/api/v1/registerdevice/';

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
	echo $response;
	curl_close($ch);
