<?php

namespace OCA\SensorLogger;
use OCP\AppFramework\Db\Entity;

/**
 * @Entity @Table(name="sensorlogger_device_groups")
 */
class Group extends Entity implements \JsonSerializable {

	/**
	 * @Id @Column(type="integer") @GeneratedValue
	 **/
	protected $id;

	/**
	 * @Column(type="string")
	 **/
	protected $userId;

	/**
	 * @Column(type="string")
	 **/
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