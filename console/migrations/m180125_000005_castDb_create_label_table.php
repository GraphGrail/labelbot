<?php

class m180125_000005_castDb_create_label_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('label', [
            'id' => "int(10) unsigned NOT NULL", 
            'label_group_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'text' => "varchar(300) NULL DEFAULT NULL", 
            'next_label_group_id' => "int(10) unsigned NULL DEFAULT NULL", 
            'ordering' => "int(11) NOT NULL DEFAULT 0", 
            'created_at' => "int(11) NULL DEFAULT NULL", 
            'updated_at' => "int(11) NULL DEFAULT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_label', 'label', ["id"]);
        $this->alterColumn('label', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('label_group_id', 'label', ["label_group_id"], false);

    }

    public function down()
    {
        $this->dropTable("label");
    }

}