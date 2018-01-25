<?php

class m180125_000006_castDb_create_assigned_label_table extends \yii\db\Migration
{

    public function up()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('assigned_label', [
            'id' => "int(10) unsigned NOT NULL", 
            'data_id' => "int(10) unsigned NOT NULL", 
            'label_id' => "int(10) unsigned NOT NULL", 
            'moderator_id' => "int(10) unsigned NOT NULL", 
            'created_at' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_assigned_label', 'assigned_label', ["id"]);
        $this->alterColumn('assigned_label', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('data_id', 'assigned_label', ["data_id"], false);
        $this->createIndex('label_id', 'assigned_label', ["label_id"], false);
        $this->createIndex('moderator_id', 'assigned_label', ["moderator_id"], false);

    }

    public function down()
    {
        $this->dropTable("assigned_label");
    }

}