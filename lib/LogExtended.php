<?php

namespace OCA\SensorLogger;

use OCP\AppFramework\Db\Entity;

class LogExtended extends Entity implements \JsonSerializable {

	protected $dataTypeId;
	protected $value;

	/**
	 * LogExtended constructor.
	 *
	 * @param $data
	 */
	public function __construct($data) {
			$this->addType('dataTypeId', 'integer');
			$this->addType('value', 'float');
			$this->dataTypeId = $data->dataTypeId;
			$this->value = $data->value;
	}

	public function jsonSerialize() {
		return [
			'dataTypeId' => $this->dataTypeId,
			'value' => $this->value
		];
	}

	/**
	 * @return mixed
	 */
	public function getDataTypeId() {
		return $this->dataTypeId;
	}

	/**
	 * @param mixed $dataTypeId
	 */
	public function setDataTypeId($dataTypeId) {
		$this->dataTypeId = $dataTypeId;
	}
	
	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}