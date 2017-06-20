<?php
/**
 * ownCloud - sensorlogger
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author ELExG <elexgspot@gmail.com>
 * @copyright ELExG 2017
 */

namespace OCA\SensorLogger\AppInfo;

use OCA\SensorLogger\SensorLogs;
use OCP\AppFramework\App;
use OCP\Util;

require_once __DIR__ . '/autoload.php';

\OCP\App::registerAdmin('files', 'admin');


$app = new App('sensorlogger');
$container = $app->getContainer();

$config = \OC::$server->getConfig();

/*
$container->query('OCP\INavigationManager')->add(function () use ($container) {
	$urlGenerator = $container->query('OCP\IURLGenerator');
	$l10n = $container->query('OCP\IL10N');
	return [
		'id' => 'sensorlogger',
		'order' => 10,
		'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.index'),
		'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
		'name' => $l10n->t('Sensor Logger'),
	];
});
*/
\OC::$server->getNavigationManager()->add(function () {
	$urlGenerator = \OC::$server->getURLGenerator();
	$l = \OC::$server->getL10N('sensorlogger');
	return [
		'id' => 'sensorlogger',
		'order' => 10,
		'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.index'),
		'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
		'name' => $l->t('SensorLogger'),
	];
});


$urlGenerator = $container->query('OCP\IURLGenerator');
$l10n = $container->query('OCP\IL10N');

\OCA\SensorLogger\App::getNavigationManager()->add(
	array(
		'id' => 'showDashboard',
		'appName' => 'sensorlogger',
		'order' => 0,
		'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.showDashboard'),
		'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
		'name' => $l10n->t('Dashboard'),
	)
);

\OCA\SensorLogger\App::getNavigationManager()->add(
	array(
		'id' => 'showList',
		'appName' => 'sensorlogger',
		'order' => 1,
		'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.showList'),
		'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
		'name' => $l10n->t('List'),
	)
);

\OCA\SensorLogger\App::getNavigationManager()->add(
	array(
		'id' => 'deviceList',
		'appName' => 'sensorlogger',
		'order' => 2,
		'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceList'),
		'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
		'name' => $l10n->t('Devices'),
	)
);

\OCA\SensorLogger\App::getNavigationManager()->add(
	array(
		'id' => 'deviceTypeList',
		'appName' => 'sensorlogger',
		'order' => 3,
		'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceTypeList'),
		'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
		'name' => $l10n->t('Device Types'),
	)
);

\OCA\SensorLogger\App::getNavigationManager()->add(
	array(
		'id' => 'deviceGroupList',
		'appName' => 'sensorlogger',
		'order' => 4,
		'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.deviceGroupList'),
		'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
		'name' => $l10n->t('Device Groups'),
	)
);

\OCA\SensorLogger\App::getNavigationManager()->add(
	array(
		'id' => 'dataTypeList',
		'appName' => 'sensorlogger',
		'order' => 5,
		'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.dataTypeList'),
		'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
		'name' => $l10n->t('Data Types'),
	)
);


if ($config->getAppValue('core', 'shareapi_enabled', 'yes') === 'yes') {

	\OCA\SensorLogger\App::getNavigationManager()->add(
		array(
			'id' => 'sharingin',
			'appName' => 'sensorlogger',
			'order' => 10,
			'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharingIn'),
			'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
			'name' => $l10n->t('Shared with you'),
		)
	);

	\OCA\SensorLogger\App::getNavigationManager()->add(
		[
			'id' => 'sharingout',
			'appName' => 'sensorlogger',
			'order' => 10,
			'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharingOut'),
			'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
			'name' => $l10n->t('Shared with others'),
		]
	);
	

	if ($config->getAppValue('core', 'shareapi_allow_links', 'yes') === 'yes') {
		\OCA\SensorLogger\App::getNavigationManager()->add(
			[
				'id' => 'sharinglinks',
				'appName' => 'sensorlogger',
				'order' => 10,
				'href' => $urlGenerator->linkToRoute('sensorlogger.sensorlogger.sharedLink'),
				'icon' => $urlGenerator->imagePath('sensorlogger', 'app.svg'),
				'name' => $l10n->t('Shared by link'),
			]
		);
	}
}