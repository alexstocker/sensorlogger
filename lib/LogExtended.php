<?php

namespace OCA\SensorLogger;

use OCP\AppFramework\Db\Entity;

class LogExtended extends Entity implements \JsonSerializable {

	protected $dataTypeId;
	protected $value;

	public $description;
	public $type;
	public $short;

    /**
     * LogExtended constructor.
     *
     * @param $data
     */
    public function __construct($data) {
        $this->addType('dataTypeId', 'integer');
        $this->addType('value', 'float');
        $this->addType('short', 'string');
        $this->addType('type', 'string');
        $this->addType('description', 'string');
        $this->dataTypeId = $data->dataTypeId;
        $this->value = $data->value;
    }

	/**
	 * @param mixed $short
	 */
	public function setShort($short) {
		$this->short = $short;
	}

	/**
	 * @return mixed
	 */
	public function getShort() {
		return $this->short;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}

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