<?php

namespace OCA\SensorLogger;


/**
 * Class Error
 *
 * @package OCA\SensorLogger
 */
class Error extends \ErrorException {
	const UNKNOWN = 9999;
	const MISSING_PARAM = 9001;
	const NOT_FOUND = 9404;
	const NOT_ALLOWED = 9405;
	const DEVICE_EXISTS = 9002;
}