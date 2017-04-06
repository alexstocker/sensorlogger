<?php
	$url = 'http://nextcloud11.loc/index.php/apps/sensorlogger/api/v1/registerdevice/';

	$data_json = "{
	  \"_route\":\"sensorlogger.apisensorlogger.registerDevice\",
	  \"deviceId\":\"6e643ee8-0f9f-11e7-93ae-92361f002671\",
	  \"deviceName\":\"Multi data sensor V2\",
	  \"deviceType\": \"Indoor\",
	  \"deviceGroup\": \"Wohnzimmer\",
	  \"deviceParentGroup\": \"Wohnung\",
	  \"deviceDataTypes\": [
		{
		  \"type\": \"temperature\",
		  \"description\": \"Temperature\",
		  \"unit\": \"°C\"
		},
		{
		  \"type\": \"humidity\",
		  \"description\": \"Humidity\",
		  \"unit\": \"% r.F.\"
		},
		{
		  \"type\": \"co2\",
		  \"description\": \"Carbon dioxide\",
		  \"unit\": \"ppm\"
		}
	  ]
	}";

	$username = 'test';
	$token = 'TJWRN-AGRIW-FXCZW-EQXFX';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
	curl_close($ch);
