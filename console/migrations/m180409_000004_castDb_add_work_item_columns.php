<?php

class m180409_000004_castDb_add_work_item_columns extends \yii\db\Migration
{

    public function up()
    {
        $this->dropPrimaryKey('PRIMARY', 'lock_entity');
        $this->addColumn('assigned_label', 'work_item_id', "int(10) unsigned NULL DEFAULT NULL AFTER moderator_id");
        $this->alterColumn('assigned_label', 'status', "tinyint(3) unsigned NOT NULL AFTER work_item_id");
        $this->addColumn('lock_entity', 'id', "int(11) NOT NULL");
        $this->alterColumn('lock_entity', 'entityName', "varchar(255) NOT NULL AFTER id");
        $this->addPrimaryKey('primary_lock_entity', 'lock_entity', ["id"]);
        $this->alterColumn('lock_entity', 'id', "int(11) NOT NULL AUTO_INCREMENT");
    }

    public function down()
    {
        $this->dropPrimaryKey('PRIMARY', 'lock_entity');
        $this->dropColumn('assigned_label', 'work_item_id');
        $this->dropColumn('lock_entity', 'id');
        $this->alterColumn('assigned_label', 'status', "tinyint(3) unsigned NOT NULL AFTER moderator_id");
        $this->alterColumn('lock_entity', 'entityName', "varchar(255) NOT NULL");
        $this->addPrimaryKey('primary_lock_entity', 'lock_entity', ["entityName","entityPk"]);
    }

}