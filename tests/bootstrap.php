<?php

/**
 * ownCloud - myapp
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author ELExG <elexgspot@gmail.com>
 * @copyright ELExG 2018
 */

$ownCloudRoot = getenv('OWNCLOUD_ROOT') ?: dirname(__DIR__, 3);
$coreBootstrap = rtrim($ownCloudRoot, DIRECTORY_SEPARATOR) . '/lib/base.php';
if (is_file($coreBootstrap)) {
    require_once $coreBootstrap;
}

require_once __DIR__ . '/../appinfo/autoload.php';
