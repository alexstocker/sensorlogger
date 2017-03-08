<?php

namespace OCA\SensorLogger;

class Widgets {

	const WIDGET_TYPES = [
		'list' => 'Data list',
		'chart' => 'Chart',
		'last'	=> 'Current data'
		];

	protected $deviceId;

	/**
	 * Widgets constructor.
	 *
	 * @param $deviceId
	 */
	public function __construct($deviceId) {
		$this->deviceId = $deviceId;
	}
	
	public function getWidgetTypes(){
		return Widgets::WIDGET_TYPES;
	}

}