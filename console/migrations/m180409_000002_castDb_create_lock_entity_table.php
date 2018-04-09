<?php

class m180409_000002_castDb_create_lock_entity_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE latin1_swedish_ci";

        $this->createTable('lock_entity', [
            'entityName' => "varchar(255) NOT NULL", 
            'entityPk' => "int(10) unsigned NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_lock_entity', 'lock_entity', ["entityName","entityPk"]);
        $this->createIndex('lock_entity_entityName_entityPk_uindex', 'lock_entity', ["entityName","entityPk"], true);

    }

    public function down()
    {
        $this->dropTable("lock_entity");
    }

}