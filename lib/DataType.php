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
		$this->addType('short','short');
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
}