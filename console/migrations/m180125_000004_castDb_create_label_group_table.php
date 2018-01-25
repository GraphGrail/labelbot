<?php

class m180125_000004_castDb_create_label_group_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('label_group', [
            'id' => "int(10) unsigned NOT NULL", 
            'user_id' => "int(10) unsigned NOT NULL", 
            'name' => "varchar(300) NOT NULL", 
            'description' => "text NOT NULL", 
            'status' => "int(11) NOT NULL", 
            'created_at' => "int(11) NOT NULL", 
            'updated_at' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_label_group', 'label_group', ["id"]);
        $this->alterColumn('label_group', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('user_id', 'label_group', ["user_id"], false);

    }

    public function down()
    {
        $this->dropTable("label_group");
    }

}