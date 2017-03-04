<?php
$url = 'http://owncloud.loc/index.php/apps/sensorlogger/api/v1/registerdevice/';

$humidity = mt_rand (1.00*10, 99.99*10) / 10;
$temperature = mt_rand (-9.00*10, 49.99*10) / 10;

$array = array("deviceId" => "f7645058-fe8c-11e6-bc64-92361f002671",
				"temperature" => $temperature,
				"humidity" => $humidity,
				"date" => date('Y-m-d H:i:s'));
$data_json = "{
  \"_route\":\"sensorlogger.apisensorlogger.registerDevice\",
  \"deviceId\":\"231d2508-786d-400d-bc35-0273a38f664d\",
  \"deviceName\":\"Multi data sensor\",
  \"deviceType\": \"Indoor\",
  \"deviceGroup\": \"Wohnzimmer\",
  \"deviceParentGroup\": \"Wohnung Wien\",
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

$username = 'admin';
$token = 'JJKHU-HSYPO-WBAJE-IHERV';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $token);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response  = curl_exec($ch);
curl_close($ch);
