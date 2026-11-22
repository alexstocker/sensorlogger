#!/usr/bin/env bash

set -euo pipefail

usage() {
    cat <<'USAGE'
Usage: scripts/sensorlogger-demo.sh <basic|extended> [interval-seconds]

Repeatedly sends simulated SensorLogger readings until interrupted.
For extended readings, register a device first and set SENSORLOGGER_DEVICE_ID.
Set SENSORLOGGER_USERNAME and SENSORLOGGER_APP_PASSWORD for API authentication.
USAGE
}

mode=${1:-}
interval=${2:-5}

if [[ "$mode" == "-h" || "$mode" == "--help" || -z "$mode" ]]; then
    usage
    exit 0
fi

if [[ "$mode" != "basic" && "$mode" != "extended" ]]; then
    usage >&2
    exit 2
fi

if ! [[ "$interval" =~ ^[1-9][0-9]*$ ]]; then
    echo "Interval must be a positive whole number of seconds." >&2
    exit 2
fi

script_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)

while true; do
    php "$script_dir/sensorlogger-demo.php" "$mode"
    sleep "$interval"
done
