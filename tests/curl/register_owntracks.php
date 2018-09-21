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
		'deviceName' => 'Wiko Getaway',
		'deviceType' => 'Mobile Fon',
		'deviceGroup' => 'GPS',
		'deviceParentGroup' => 'Wearables',
		'deviceDataTypes' => [
		    [
			'type' => 'lat',
			'description' => 'Location latitude',
			'unit' => 'deg'
		],
		[
			'type' => 'lon',
			'description' => 'Location longitude',
			'unit' => 'deg'
		],
		[
			'type' => 'acc',
			'description' => 'Accuracy',
			'unit' => 'm'
		],
		[
			'type' => 'batt',
			'description' => 'Battery Level',
			'unit' => 'prc'
		],
		[
			'type' => 'alt',
			'description' => 'Altitude',
			'unit' => 'm'
		],
		[
			'type' => 'rad',
			'description' => 'Radius',
			'unit' => 'm'
		]]
	];

	$data_json = json_encode($registerArray);

	$username = 'admin';
	$token = 'PDCEW-PNUQL-DEBSF-JKBTP';

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
