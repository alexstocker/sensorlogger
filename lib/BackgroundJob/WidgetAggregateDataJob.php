<?php

namespace OCA\SensorLogger\Cron;

use OC\BackgroundJob\TimedJob;
use OCP\IConfig;

class WidgetAggregateDataJob extends TimedJob {

	protected $config;
	public function __construct() {
		$this->setInterval(0);
	}

	protected function run($argument) {
		//$connection = \OC::$server->getDatabaseConnection();
		//$logger = \OC::$server->getLogger();
		//$configs = $this->config->getAppKeys('sensorlogger');

		$now = new \DateTime();
		$now = $now->format('Y-m-d H:i:s');

		\OCP\Util::writeLog('sensorlogger', "BLABLA background job, the job took : 0 seconds, " .
			"this job is an instance of class : " . \get_class($this) . ' with arguments : ' . \print_r($this->argument, true), \OCP\Util::DEBUG);


		//var_dump($configs);

		/*
		$qb = $connection->getQueryBuilder();
		$qb->select('id', 'file_source', 'uid_owner', 'item_type')
			->from('share')
			->where(
				$qb->expr()->andX(
					$qb->expr()->eq('share_type', $qb->expr()->literal(\OCP\Share::SHARE_TYPE_LINK)),
					$qb->expr()->lte('expiration', $qb->expr()->literal($now)),
					$qb->expr()->orX(
						$qb->expr()->eq('item_type', $qb->expr()->literal('file')),
						$qb->expr()->eq('item_type', $qb->expr()->literal('folder'))
					)
				)
			);

		$shares = $qb->execute();
		while ($share = $shares->fetch()) {
			\OCP\Share::unshare($share['item_type'], $share['file_source'], \OCP\Share::SHARE_TYPE_LINK, null, $share['uid_owner']);
		}
		$shares->closeCursor();

		return true;
		*/
	}
}