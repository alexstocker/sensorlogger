#!/bin/bash
create_log_url="http://owncloud-dev.loc/index.php/apps/sensorlogger/api/v1/createlog/"
register_device_url="http://owncloud-dev.loc/index.php/apps/sensorlogger/api/v1/registerdevice/"
usage="$(basename "$0") [-h] OR [-s] OR [-r] OR [-e] -- demo script to post sensor data and register device to SensorLogger

where:
    -h  show this help text
    -e  post extend sensor data. Register device is mandatory to make use of this.
    -r  register a device with data types"

device_uuid="0002-0655-0000-0000-0000-0002"
user="admin"
token="MFSFF-VREYH-PHSLT-DTMFN"
interval=5

register_device() {
    json_string='{"deviceId":"'${device_uuid}'","deviceDataTypes":[{"type":"cpu-core-temp-0","description":"CoreTemp0","unit":"°C"},{"type":"cpu-core-temp-1","description":"CoreTemp1","unit":"°C"}]}'

    clear
    echo "-- Register a device --"
    curl -k \
    -H "Accept: application/json" \
    -H "Content-Type:application/json" \
    -u ${user}:${token} \
    -X POST -d ${json_string} ${register_device_url}
}

function collect_data {
core_temp0_raw=$(cat /sys/devices/platform/coretemp.0/hwmon/hwmon0/temp2_input)
core_temp0=$(expr $core_temp0_raw / 1000)

core_temp1_raw=$(cat /sys/devices/platform/coretemp.0/hwmon/hwmon0/temp4_input)
core_temp1=$(expr $core_temp1_raw / 1000)

json_string='{"deviceId":"'${device_uuid}'","data":[{"dataTypeId":"6","value":"'${core_temp0}'"},{"dataTypeId":"7","value":"'${core_temp1}'"}]}'

curl -k \
-H "Accept: application/json" \
-H "Content-Type:application/json" \
-u ${user}:${token} \
-X POST -d ${json_string} ${create_log_url}
}

create_log_extended() {
    while true; do clear; collect_data; sleep ${interval}; done;
}

#sensors
while getopts "hres" OPTION ; do
  case $OPTION in
    h)
       echo "$usage"
       exit 0
       ;;
    r)
       register_device
       ;;
    e)
       create_log_extended
       ;;
   \?) printf "illegal option: " "$OPTARG" >&2
       echo "$usage" >&2
       exit 1
       ;;
  esac
done