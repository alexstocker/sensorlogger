<?php

namespace OCA\SensorLogger;

use \OCP\IContainer;

class App {
	/**
	 * @var \OCP\INavigationManager
	 */
	private static $navigationManager;

	public function __construct(array $urlParams = array())
    {
        parent::__construct($urlParams);
        $container = $this->getContainer();
        $server = $container->getServer();

        if($container) {
            $container->registerCapability('OCA\SensorLogger\Capabilities');
        }

    }

    /**
	 * Returns the app's navigation manager
	 *
	 * @return \OCP\INavigationManager
	 */
	public static function getNavigationManager() {
		// TODO: move this into a service in the Application class
		if (self::$navigationManager === null) {
			self::$navigationManager = new \OC\NavigationManager();
		}
		return self::$navigationManager;
	}



}
