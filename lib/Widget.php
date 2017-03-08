<?php

namespace OCA\SensorLogger;

class Widget {

	protected $deviceId;
	protected $type;

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getDeviceId() {
		return $this->deviceId;
	}

	/**
	 * @param mixed $deviceId
	 */
	public function setDeviceId($deviceId) {
		$this->deviceId = $deviceId;
	}
}