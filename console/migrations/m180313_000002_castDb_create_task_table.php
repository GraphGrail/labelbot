<?php

class m180313_000002_castDb_create_task_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('task', [
            'id' => "int(10) unsigned NOT NULL", 
            'user_id' => "int(10) unsigned NOT NULL", 
            'dataset_id' => "int(10) unsigned NOT NULL", 
            'label_group_id' => "int(10) unsigned NOT NULL", 
            'description' => "varchar(255) NOT NULL", 
            'created_at' => "int(11) NOT NULL", 
            'updated_at' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_task', 'task', ["id"]);
        $this->createIndex('user_id', 'task', ["user_id"], false);
        $this->createIndex('dataset_id', 'task', ["dataset_id"], false);

    }

    public function down()
    {
        $this->dropTable("task");
    }

}