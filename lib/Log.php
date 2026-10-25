<?php

namespace OCA\SensorLogger;
use OCP\AppFramework\Db\Entity;

class Log extends Entity implements \JsonSerializable {

	protected $deviceUuid;
	protected $userId;
	protected $temperature;
	protected $humidity;
	protected $data;
	protected $createdAt;

	public function __construct() {
		$this->addType('deviceUuid', 'string');
		$this->addType('userId', 'string');
		$this->addType('temperature', 'float');
		$this->addType('humidity', 'float');
		$this->addType('data', 'string');
		$this->addType('createdAt', 'string');
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'deviceUuid' => $this->deviceUuid,
			'userId' => $this->userId,
			'temperature' => $this->temperature,
			'humidity' => $this->humidity,
			'data' => $this->data,
			'createdAt' => $this->createdAt,
		];
	}

	/**
	 * @return mixed
	 */
	public function getTemperature() {
		return $this->temperature;
	}

	/**
	 * @param mixed $temperature
	 */
	public function setTemperature($temperature) {
		$this->temperature = number_format($temperature,2);
	}

	/**
	 * @return mixed
	 */
	public function getHumidity() {
		return $this->humidity;
	}

	/**
	 * @param mixed $humidity
	 */
	public function setHumidity($humidity) {
		$this->humidity = number_format($humidity,2);
	}

	/**
	 * @return LogExtended[]
	 */
	public function getData() {
		if (is_string($this->data)) {
			return $this->decodeData($this->data);
		}

		return is_array($this->data) ? $this->data : [];
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data) {
		$this->data = $this->decodeData($data);
	}

	private function decodeData($data) {
		$dataTypes = json_decode($data);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \UnexpectedValueException('Sensor log data contains invalid JSON.');
		}
		if (!is_iterable($dataTypes)) {
			return [];
		}

		$array = [];
		foreach ($dataTypes as $dataType) {
			if (is_object($dataType) && isset($dataType->dataTypeId, $dataType->value)) {
				$array[] = new LogExtended($dataType);
			}
		}

		return $array;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @param mixed $createdAt
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}
}