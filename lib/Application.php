<?php
/**
 * @author Alexander Stocker <alex@stocker.info>
 *
 * @copyright Copyright (c) 2017, Alexander Stocker
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\SensorLogger;

use OCA\SensorLogger\Controller\ApiSensorLoggerController;
use OCA\SensorLogger\Controller\SensorLoggerController;
use OCP\AppFramework\App;
use OCP\IContainer;

/**
 * Class Application
 *
 * @package OCA\SensorLogger\AppInfo
 */
class Application extends App {

	public function __construct(array $urlParams = array()) {
		parent::__construct('sensorlogger', $urlParams);
		$container = $this->getContainer();
		$server = $container->getServer();

		$container->registerService('sensorloggercontroller', function (IContainer $c) use ($server) {
			return new SensorLoggerController(
				$c->query('AppName'),
				$c->query('Request'),
				$server->getURLGenerator(),
				$server->getNavigationManager(),
				$c->query('L10N'),
				$server->getDatabaseConnection(),
				$server->getConfig(),
				$server->getEventDispatcher(),
				$server->getUserSession(),
				$server->getAppManager()
			);
		});

		$container->registerService('apisensorloggercontroller', function (IContainer $c) use ($server) {
			return new ApiSensorLoggerController(
				$c->query('AppName'),
				$c->query('Request'),
				$server->getDatabaseConnection(),
				$server->getConfig(),
				$server->getShareManager(),
				$server->getGroupManager(),
				$server->getUserManager(),
				$c->query('L10N'),
				$server->getUserSession()
			);
		});


		/**
		 * Core
		 */
		$container->registerService('L10N', function (IContainer $c) {
			return $c->query('ServerContainer')->getL10N($c->query('AppName'));
		});

        $container->registerCapability('OCA\SensorLogger\Capabilities');
	}



}