<?php
namespace OCA\sensorlogger\Migrations;

use Doctrine\DBAL\Schema\Schema;
use OCP\Migration\ISchemaMigration;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version20190206015249 implements ISchemaMigration {

    /**
     * @param Schema $schema
     * @param array $options
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
	public function changeSchema(Schema $schema, array $options) {
        $this->prefix = $options['tablePrefix'];
        if ($schema->hasTable("{$this->prefix}sensorlogger_devices")) {
            $table = $schema->getTable("{$this->prefix}sensorlogger_devices");
            $table->addUniqueIndex(['user_id','uuid'],'sensorlogger_devices_unique_idx');
        }
    }
}
