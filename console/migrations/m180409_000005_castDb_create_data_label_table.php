<?php

class m180409_000005_castDb_create_data_label_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('data_label', [
            'id' => "int(10) unsigned NOT NULL", 
            'task_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'work_item_id' => "int(10) unsigned NOT NULL", 
            'data_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'label_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'moderator_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'status' => "tinyint(3) unsigned NOT NULL", 
            'created_at' => "int(11) NOT NULL", 
            'updated_at' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_data_label', 'data_label', ["id"]);
        $this->alterColumn('data_label', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('moderator_id', 'data_label', ["moderator_id"], false);
        $this->createIndex('dataset_id', 'data_label', ["task_id"], false);
        $this->createIndex('work_item_id', 'data_label', ["work_item_id"], false);

    }

    public function down()
    {
        $this->dropTable("data_label");
    }

}