<?php

class m180406_000002_castDb_create_queue_task_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE latin1_swedish_ci";

        $this->createTable('queue_task', [
            'id' => "int(10) unsigned NOT NULL", 
            'task_id' => "int(10) unsigned NOT NULL", 
            'job' => "text NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_queue_task', 'queue_task', ["id"]);
        $this->alterColumn('queue_task', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('queue_task_task_id_index', 'queue_task', ["task_id"], false);

    }

    public function down()
    {
        $this->dropTable("queue_task");
    }

}