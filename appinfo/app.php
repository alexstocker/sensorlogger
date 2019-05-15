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

use OCA\SensorLogger\SensorLogs;
use OCP\AppFramework\App;
use OCP\Util;

require_once __DIR__ . '/autoload.php';

\OCP\App::registerAdmin('sensorlogger', 'admin');
\OC::$server->getJobList()->add('OCA\SensorLogger\Cron\WidgetAggregateDataJob');


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


//\OCP\Util::connectHook('\OCP\Config', 'js', '\OCA\Files\App', 'extendJsConfig');
