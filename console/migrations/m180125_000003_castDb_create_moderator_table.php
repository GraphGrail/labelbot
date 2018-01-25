<?php

class m180125_000003_castDb_create_moderator_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('moderator', [
            'id' => "int(10) unsigned NOT NULL", 
            'auth_token' => "varchar(64) NULL DEFAULT NULL", 
            'tg_chat_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'tg_id' => "int(11) NOT NULL", 
            'tg_username' => "varchar(200) NULL DEFAULT NULL", 
            'tg_first_name' => "varchar(200) NULL DEFAULT NULL", 
            'tg_last_name' => "varchar(200) NULL DEFAULT NULL", 
            'phone' => "varchar(20) NULL DEFAULT NULL", 
            'created_at' => "int(11) NOT NULL", 
            'updated_at' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_moderator', 'moderator', ["id"]);
        $this->alterColumn('moderator', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");

    }

    public function down()
    {
        $this->dropTable("moderator");
    }

}