<?php

namespace OCA\SensorLogger;

use OCP\AppFramework\Db\Entity;

class Type extends Entity implements \JsonSerializable
{
    protected $id;

    protected $userId;

    protected $deviceTypeName;

    protected $assigendDevices;

    public function __construct()
    {
        $this->addType('userId', 'string');
        $this->addType('deviceTypeName', 'string');
        $this->addType('assignedDevices', 'array');
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'deviceTypeName' => $this->deviceTypeName,
            'assignedDevices' => $this->assigendDevices
        ];
    }
}
