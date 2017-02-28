<?php
/**
 * Created by PhpStorm.
 * User: elex
 * Date: 19.02.17
 * Time: 15:28
 */

namespace OCA\SensorLogger\Controller;


use OCP\AppFramework\Controller;
use OCP\IConfig;

class SensorLog {

	private $config;
	private $appName;
	public function __construct(IConfig $config, $appName){
		$this->config = $config;
		$this->appName = $appName;
	}
	public function getUserValue($key, $userId) {
		return $this->config->getUserValue($userId, $this->appName, $key);
	}
	public function setUserValue($key, $userId, $value) {
		$this->config->setUserValue($userId, $this->appName, $key, $value);
	}
}