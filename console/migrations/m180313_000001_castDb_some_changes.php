<?php

class m180313_000001_castDb_some_changes extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('assigned_label', 'task_id', "int(10) unsigned NULL DEFAULT NULL AFTER id");
        $this->alterColumn('assigned_label', 'data_id', "int(10) unsigned NOT NULL AFTER task_id");
        $this->addColumn('moderator', 'eth_addr', "varchar(42) NULL DEFAULT NULL AFTER auth_token");
        $this->alterColumn('moderator', 'tg_chat_id', "int(10) unsigned NULL DEFAULT NULL AFTER eth_addr");
        $this->createIndex('dataset_id', 'assigned_label', ["task_id"], false);
    }

    public function down()
    {
        $this->dropIndex('dataset_id', 'assigned_label');
        $this->dropColumn('assigned_label', 'task_id');
        $this->dropColumn('moderator', 'eth_addr');
        $this->alterColumn('assigned_label', 'data_id', "int(10) unsigned NOT NULL AFTER id");
        $this->alterColumn('moderator', 'tg_chat_id', "int(10) unsigned NULL DEFAULT NULL AFTER auth_token");
    }

}