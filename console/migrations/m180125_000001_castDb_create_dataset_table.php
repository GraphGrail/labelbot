<?php

class m180125_000001_castDb_create_dataset_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('dataset', [
            'id' => "int(10) unsigned NOT NULL", 
            'user_id' => "int(10) unsigned NOT NULL", 
            'name' => "varchar(200) NOT NULL", 
            'description' => "text NOT NULL", 
            'status' => "int(11) NULL DEFAULT NULL", 
            'created_at' => "int(11) NOT NULL", 
            'updated_at' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_dataset', 'dataset', ["id"]);
        $this->alterColumn('dataset', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('user_id', 'dataset', ["user_id"], false);

    }

    public function down()
    {
        $this->dropTable("dataset");
    }

}