<?php

namespace OCA\SensorLogger;
use OCP\AppFramework\Db\Entity;

/** @Entity */
class Device extends Entity implements \JsonSerializable {
	protected $uuid;
	protected $userId;
	protected $name;
	protected $type;
	protected $group;
	protected $groupParent;
	protected $typeName;
	protected $groupName;
	protected $groupParentName;


	/**
	 * Many Devices have Many Groups.
	 * @ManyToMany(targetEntity="Group")
	 * @JoinTable(name="sensorlogger_device_groups",
	 *      joinColumns={@JoinColumn(name="group", referencedColumnName="id")},
	 *      inverseJoinColumns={@JoinColumn(name="id", referencedColumnName="group", unique=false)}
	 *      )
	 */
	//protected $group;

	public function __construct()
	{
		$this->addType('uuid', 'string');
		$this->addType('userId', 'integer');
		$this->addType('name', 'string');
		$this->addType('type', 'integer');
		$this->addType('group', 'integer');
		$this->addType('groupParent', 'integer');
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
			'type' => $this->type,
			'group' => $this->group,
			'groupParent' => $this->groupParent,
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