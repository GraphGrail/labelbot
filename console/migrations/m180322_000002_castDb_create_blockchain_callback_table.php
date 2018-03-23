<?php

class m180322_000002_castDb_create_blockchain_callback_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('blockchain_callback', [
            'id' => "int(10) unsigned NOT NULL", 
            'callback_id' => "varchar(32) NOT NULL", 
            'received' => "tinyint(1) NOT NULL DEFAULT 0", 
            'success' => "tinyint(1) NULL DEFAULT NULL", 
            'error' => "text NULL DEFAULT NULL", 
            'payload' => "text NULL DEFAULT NULL", 
            'created_at' => "int(11) NOT NULL", 
            'updated_at' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_blockchain_callback', 'blockchain_callback', ["id"]);
        $this->alterColumn('blockchain_callback', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('callback_id', 'blockchain_callback', ["callback_id"], true);

    }

    public function down()
    {
        $this->dropTable("blockchain_callback");
    }

}