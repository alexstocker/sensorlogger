<?php

namespace OCA\SensorLogger;
use OCP\AppFramework\Db\Entity;

/** @Entity */
class Group extends Entity implements \JsonSerializable {

	protected $userId;
	protected $deviceGroupName;

	public function __construct() {
		$this->addType('userId', 'string');
		$this->addType('deviceGroupName', 'string');
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'userId' => $this->userId,
			'groupName' => $this->deviceGroupName
			];
	}
}