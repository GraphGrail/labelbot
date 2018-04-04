<?php

class m180404_000001_castDb_drop_label_group_to_dataset extends \yii\db\Migration
{

    public function up()
    {
        $this->dropTable("label_group_to_dataset");
    }

    public function down()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_general_ci";

        $this->createTable('label_group_to_dataset', [
            'id' => "int(10) unsigned NOT NULL", 
            'label_group_id' => "int(11) NOT NULL", 
            'dataset_id' => "int(11) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_label_group_to_dataset', 'label_group_to_dataset', ["id"]);
        $this->alterColumn('label_group_to_dataset', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('label_group_id', 'label_group_to_dataset', ["label_group_id"], false);
        $this->createIndex('dataset_id', 'label_group_to_dataset', ["dataset_id"], false);

    }

}