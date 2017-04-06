<?php
	$url = 'http://owncloud.loc/index.php/apps/sensorlogger/api/v1/registerdevice/';

	$data_json = "{
	  \"_route\":\"sensorlogger.apisensorlogger.registerDevice\",
	  \"deviceId\":\"99999999-786d-400d-bc35-0273a38f664d\",
	  \"deviceName\":\"Air Quality (VOCs)\",
	  \"deviceType\": \"Outdoor\",
	  \"deviceGroup\": \"Bedroom\",
	  \"deviceParentGroup\": \"Isengaard\",
	  \"deviceDataTypes\": [
		{
		  \"type\": \"CO\",
		  \"description\": \"Carbon monoxide\",
		  \"unit\": \"ppm\"
		},
		{
		  \"type\": \"C2H6OH\",
		  \"description\": \"Ethanol\",
		  \"unit\": \"ppm\"
		},
		{
		  \"type\": \"H2\",
		  \"description\": \"Hydrogen\",
		  \"unit\": \"ppm\"
		},
		{
		  \"type\": \"NH3\",
		  \"description\": \"Ammonia\",
		  \"unit\": \"ppm\"
		},
		{
		  \"type\": \"CH4\",
		  \"description\": \"Methane\",
		  \"unit\": \"ppm\"
		}
	  ]
	}";

	$username = 'test';
	$token = 'HTFQO-WCNSR-UDEVJ-EPLYE';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
	curl_close($ch);
