<?php

namespace OCA\SensorLogger;

use OCP\AppFramework\Db\Entity;

/**
 * Class Widget
 *
 * @package OCA\SensorLogger
 */
class Widget extends Entity implements \JsonSerializable {

	protected $deviceId;
	protected $type;
	protected $log;
	protected $name;
	protected $displayName;
	protected $templateName = 'default';
	protected $options = [];

	public function __construct() {
		$this->addType('deviceId', 'integer');
		$this->addType('type', 'string');
		$this->addType('log', 'string');
		$this->addType('name', 'string');
		//$this->addType('sortOrder', 'integer');
		//$this->addType('options', '')
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'deviceId' => $this->deviceId,
			'type' => $this->type,
			'log' => $this->log,
			'name' => $this->name,
            'displayName' => $this->displayName,
            'templateName' => $this->templateName,
            'options' => $this->options
		];
	}

    public function widgetTemplateName()
    {
        return $this->templateName;
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
	public function getDeviceId() {
		return $this->deviceId;
	}

	/**
	 * @param mixed $deviceId
	 */
	public function setDeviceId($deviceId) {
		$this->deviceId = $deviceId;
	}

	/**
	 * @return mixed
	 */
	public function getLog() {
		return $this->log;
	}

	/**
	 * @param mixed $log
	 */
	public function setLog($log) {
		$this->log = $log;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

    /**
     * @param mixed $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}