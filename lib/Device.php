<?php

namespace OCA\SensorLogger;
use OCP\AppFramework\Db\Entity;

/**
 * Class Device
 *
 * @package OCA\SensorLogger
 */
class Device extends Entity implements \JsonSerializable {
	protected $uuid;
	protected $userId;
	protected $name;
	protected $typeId;
	protected $groupId;
	protected $groupParentId;
	protected $deviceTypeName;
	protected $deviceGroupName;
	protected $deviceGroupParentName;

	public function __construct() {
		$this->addType('uuid', 'string');
		$this->addType('userId', 'string');
		$this->addType('name', 'string');
		$this->addType('typeId', 'integer');
		$this->addType('groupId', 'integer');
		$this->addType('groupParentId', 'integer');
		$this->addType('deviceTypeName', 'string');
		$this->addType('deviceGroupName', 'string');
		$this->addType('deviceGroupParentName', 'string');
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'name' => $this->name,
			'type' => $this->typeId,
			'group' => $this->groupId,
			'groupParent' => $this->groupParentId,
			'deviceTypeName' => $this->deviceTypeName,
			'deviceGroupName' => $this->deviceGroupName,
			'deviceGroupParentName' => $this->deviceGroupParentName
		];
	}

	/**
	 * @return mixed
	 */
	public function getUuid() {
		return $this->uuid;
	}

	/**
	 * @param mixed $uuid
	 */
	public function setUuid($uuid) {
		$this->uuid = $uuid;
	}
}