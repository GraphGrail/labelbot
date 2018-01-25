<?php

class m180125_000002_castDb_create_data_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('data', [
            'id' => "int(10) unsigned NOT NULL", 
            'dataset_id' => "int(10) unsigned NOT NULL", 
            'data' => "text NULL DEFAULT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_data', 'data', ["id"]);
        $this->createIndex('dataset_id', 'data', ["dataset_id"], false);

    }

    public function down()
    {
        $this->dropTable("data");
    }

}