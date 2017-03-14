<?php

namespace OCA\SensorLogger;
use OCP\AppFramework\Db\Entity;

class DataType extends Entity implements \JsonSerializable {

	protected $userId;
	protected $description;
	protected $type;
	protected $short;

	public function __construct() {
		$this->addType('userId', 'string');
		$this->addType('description', 'string');
		$this->addType('type','string');
		$this->addType('short','string');
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'userId' => $this->userId,
			'description' => $this->description,
			'type' => $this->type,
			'short' => $this->short
			];
	}

	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
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
	public function getShort() {
		return $this->short;
	}

	/**
	 * @param mixed $short
	 */
	public function setShort($short) {
		$this->short = $short;
	}
}