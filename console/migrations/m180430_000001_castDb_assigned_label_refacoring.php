<?php

class m180430_000001_castDb_assigned_label_refacoring extends \yii\db\Migration
{

    public function up()
    {
        $this->dropIndex('moderator_id', 'data_label');
        $this->dropIndex('dataset_id', 'data_label');
        $this->dropColumn('data_label', 'task_id');
        $this->dropColumn('data_label', 'moderator_id');
        $this->dropTable("assigned_label");
        $this->addColumn('work_item', 'moderator_address', "varchar(42) NULL DEFAULT NULL AFTER moderator_id");
        $this->alterColumn('work_item', 'items', "int(11) NULL DEFAULT NULL AFTER moderator_address");
        $this->alterColumn('data_label', 'work_item_id', "int(10) unsigned NOT NULL AFTER id");
        $this->alterColumn('data_label', 'status', "tinyint(3) unsigned NOT NULL AFTER label_id");
    }

    public function down()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('assigned_label', [
            'id' => "int(10) unsigned NOT NULL", 
            'task_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'data_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'label_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'moderator_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'work_item_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'status' => "tinyint(3) unsigned NOT NULL", 
            'created_at' => "int(11) NOT NULL", 
            'updated_at' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_assigned_label', 'assigned_label', ["id"]);
        $this->alterColumn('assigned_label', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('data_id', 'assigned_label', ["data_id"], false);
        $this->createIndex('moderator_id', 'assigned_label', ["moderator_id"], false);
        $this->createIndex('label_id', 'assigned_label', ["label_id"], false);
        $this->createIndex('dataset_id', 'assigned_label', ["task_id"], false);

        $this->dropColumn('work_item', 'moderator_address');
        $this->addColumn('data_label', 'task_id', "int(10) unsigned NULL DEFAULT NULL AFTER id");
        $this->addColumn('data_label', 'moderator_id', "int(10) unsigned NULL DEFAULT NULL AFTER label_id");
        $this->alterColumn('data_label', 'work_item_id', "int(10) unsigned NOT NULL AFTER task_id");
        $this->alterColumn('data_label', 'status', "tinyint(3) unsigned NOT NULL AFTER moderator_id");
        $this->alterColumn('work_item', 'items', "int(11) NULL DEFAULT NULL AFTER moderator_id");
        $this->createIndex('moderator_id', 'data_label', ["moderator_id"], false);
        $this->createIndex('dataset_id', 'data_label', ["task_id"], false);
    }

}