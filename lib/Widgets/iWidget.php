<?php

namespace OCA\SensorLogger\Widgets;


use OC\DB\Connection;
use OCA\SensorLogger\Device;

interface iWidget
{

    public function widgetIdentifier();
    public function widgetDisplayName();
    public function widgetTemplateName();
    public function widgetData($userId, Device $device, Connection $connection);

}