<?php

declare(strict_types=1);

namespace OCA\SensorLogger\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version000009Date20190206183414 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('sensorlogger_logs')) {
			$table = $schema->createTable('sensorlogger_logs');
			$table->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('device_uuid', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 64,
				'default' => '',
			]);
			$table->addColumn('temperature', 'float', [
				'notnull' => false,
			]);
			$table->addColumn('humidity', 'float', [
				'notnull' => false,
			]);
			$table->addColumn('data', 'string', [
				'notnull' => false,
				'length' => 255,
			]);
			$table->addColumn('created_at', 'datetime', [
				'notnull' => false,
			]);
			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('sensorlogger_device_types')) {
			$table = $schema->createTable('sensorlogger_device_types');
			$table->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => false,
				'length' => 64,
			]);
			$table->addColumn('device_type_name', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('sensorlogger_device_groups')) {
			$table = $schema->createTable('sensorlogger_device_groups');
			$table->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 64,
				'default' => '',
			]);
			$table->addColumn('device_group_name', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('sensorlogger_devices')) {
			$table = $schema->createTable('sensorlogger_devices');
			$table->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 64,
				'default' => '',
			]);
			$table->addColumn('uuid', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->addColumn('name', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->addColumn('type_id', 'bigint', [
				'notnull' => true,
				'length' => 20,
				'default' => 0,
			]);
			$table->addColumn('group_id', 'bigint', [
				'notnull' => true,
				'length' => 20,
				'default' => 0,
			]);
			$table->addColumn('group_parent_id', 'bigint', [
				'notnull' => true,
				'length' => 20,
				'default' => 0,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['type_id'], 'type_id_idx');
			$table->addIndex(['group_id'], 'group_id_idx');
			$table->addIndex(['group_parent_id'], 'group_parent_id_idx');
		}

		if (!$schema->hasTable('sensorlogger_device_data_types')) {
			$table = $schema->createTable('sensorlogger_device_data_types');
			$table->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 64,
				'default' => '',
			]);
			$table->addColumn('device_id', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->addColumn('data_type_id', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('sensorlogger_data_types')) {
			$table = $schema->createTable('sensorlogger_data_types');
			$table->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 64,
				'default' => '',
			]);
			$table->addColumn('description', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->addColumn('type', 'string', [
				'notnull' => true,
				'length' => 255,
				'default' => '',
			]);
			$table->addColumn('short', 'string', [
				'notnull' => true,
				'length' => 10,
				'default' => '',
			]);
			$table->setPrimaryKey(['id']);
		}
		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}
}
