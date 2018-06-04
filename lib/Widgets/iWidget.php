<?php

namespace OCA\SensorLogger\Widgets;


use OC\DB\Connection;
use OCA\SensorLogger\Device;

interface iWidget
{

    function widgetIdentifier();
    function widgetDisplayName();
    function widgetTemplateName();
    function widgetData($userId, Device $device, Connection $connection);

}