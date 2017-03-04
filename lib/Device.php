<?php

namespace OCA\SensorLogger;
use OCP\AppFramework\Db\Entity;

/** @Entity */
class Device extends Entity implements \JsonSerializable {
	protected $uuid;
	protected $userId;
	protected $name;
	protected $typeId;
	protected $groupId;
	protected $groupParentId;
	protected $typeName;
	protected $groupName;
	protected $groupParentName;


	/**
	 * Many Devices have Many Groups.
	 * @ManyToMany(targetEntity="Group")
	 * @JoinTable(name="sensorlogger_device_groups",
	 *      joinColumns={@JoinColumn(name="group_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@JoinColumn(name="id", referencedColumnName="group_id", unique=false)}
	 *      )
	 */
	//protected $group;

	public function __construct()
	{
		$this->addType('uuid', 'string');
		$this->addType('userId', 'string');
		$this->addType('name', 'string');
		$this->addType('typeId', 'integer');
		$this->addType('groupId', 'integer');
		$this->addType('groupParentId', 'integer');
		$this->addType('typeName', 'string');
		$this->addType('groupName', 'string');
		$this->addType('groupParentName', 'string');
		//$this->group = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'name' => $this->name,
			'type' => $this->typeId,
			'group' => $this->groupId,
			'groupParent' => $this->groupParentId,
			'typeName' => $this->typeName,
			'groupName' => $this->groupName,
			'groupParentName' => $this->groupParentName
		];
	}
}

/** @Entity */
class SomeEntity extends Entity {

}

/** @Entity */
class SomeOtherEntity {

}