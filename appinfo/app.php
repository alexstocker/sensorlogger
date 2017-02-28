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

use OCP\AppFramework\App;

require_once __DIR__ . '/autoload.php';

$app = new App('sensorlogger');
$container = $app->getContainer();

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