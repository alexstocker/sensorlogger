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
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data) {
		$dataTypes = json_decode($data);

		$array = [];
		foreach($dataTypes as $dataType) {
			if($dataType->value) {
				$array[] = new LogExtended($dataType);
			}
		}
		
		$this->data = $array;
	}
}