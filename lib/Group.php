<?php

namespace OCA\SensorLogger;
use OCP\AppFramework\Db\Entity;

/** @Entity */
class Group extends Entity implements \JsonSerializable {

	protected $deviceGroupName;

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'groupName' => $this->deviceGroupName,
			];
	}
}