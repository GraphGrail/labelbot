<?php

class m180409_000001_castDb_create_work_item_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE latin1_swedish_ci";

        $this->createTable('work_item', [
            'id' => "int(10) unsigned NOT NULL", 
            'task_id' => "int(10) unsigned NOT NULL", 
            'moderator_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'items' => "int(11) NULL DEFAULT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_work_item', 'work_item', ["id"]);
        $this->alterColumn('work_item', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");

    }

    public function down()
    {
        $this->dropTable("work_item");
    }

}