<?php

namespace OCA\SensorLogger;

use OCP\DB\QueryBuilder\IQueryBuilder;

class QueryBuilderCompat {

	public static function executeQuery(IQueryBuilder $query) {
		if (method_exists($query, 'executeQuery')) {
			return $query->executeQuery();
		}

		return $query->execute();
	}

	public static function executeStatement(IQueryBuilder $query) {
		if (method_exists($query, 'executeStatement')) {
			return $query->executeStatement();
		}

		return $query->execute();
	}
}
