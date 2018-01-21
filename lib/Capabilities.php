<?php
/**
 * Created by PhpStorm.
 * User: elex
 * Date: 16.01.18
 * Time: 20:42
 */

namespace OCA\SensorLogger;

use OCP\Capabilities\ICapability;
use OCP\IConfig;

class Capabilities implements ICapability
{

    /** @var IConfig */
    protected $config;

    /**
     * Capabilities constructor.
     *
     * @param IConfig $config
     */
    public function __construct(IConfig $config) {
        $this->config = $config;
    }

    /**
     * Return this classes capabilities
     *
     * TODO: Define some use full capabilities
     * @return array
     */
    public function getCapabilities() {
        return [
            'sensorlogger' => [
                'someCapA' => true,
                'someCapB' => [],
            ],
        ];
    }

}