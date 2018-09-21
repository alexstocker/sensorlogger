<?php
	$url = 'http://owncloud10.loc/index.php/apps/sensorlogger/api/v1/owntracks/';

$data['_type'] = "location";
$data['tid'] = "aa10a702-9933-e393-7f78-e70d8ae82c0e";
$data['conn'] = "w";
$data['tst'] = time();
$data['acc'] = 16;
$data['batt'] = 34;
$data['date'] = date('Y-m-d H:i:s');
	
	$data['lat'] = mt_rand (-90.00*10, 90.00*10) / 10;
	$data['lon'] = mt_rand (-90.00*10, 90.00*10) / 10;

	$data_json = json_encode($data);

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
	var_dump($response);
	if($response === false){
		echo('Error: ' . curl_error($ch));
	}
	else{
        	echo('Operation successful');
	}
	curl_close($ch);
