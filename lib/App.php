<?php

namespace OCA\SensorLogger;

class App {
	/**
	 * @var \OCP\INavigationManager
	 */
	private static $navigationManager;

	/**
	 * Returns the app's navigation manager
	 *
	 * @return \OCP\INavigationManager
	 */
	public static function getNavigationManager() {
		// TODO: move this into a service in the Application class
		if (self::$navigationManager === null) {
			self::$navigationManager = new \OC\NavigationManager(
				\OC::$server->getAppManager(),
				\OC::$server->getURLGenerator(),
				\OC::$server->getL10NFactory(),
				\OC::$server->getUserSession(),
				\OC::$server->getGroupManager(),
				\OC::$server->getConfig()
			);
		}
		return self::$navigationManager;
	}

}
