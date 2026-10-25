<?php

namespace OCA\SensorLogger\Widgets;


use OCA\SensorLogger\Device;
use OCP\IDBConnection;

interface iWidget
{

    public function widgetIdentifier();
    public function widgetDisplayName();
    public function widgetTemplateName();
    public function widgetData($userId, Device $device, IDBConnection $connection);

}