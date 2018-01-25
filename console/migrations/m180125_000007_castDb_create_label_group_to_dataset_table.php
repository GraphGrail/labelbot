<?php

class m180125_000007_castDb_create_label_group_to_dataset_table extends \yii\db\Migration
{

    public function up()
    {
        $this->createTable('label_group_to_dataset', [
            'id' => "int(10) unsigned NOT NULL", 
            'label_group_id' => "int(11) NOT NULL", 
            'dataset_id' => "int(11) NOT NULL", 
        ]);

        $this->addPrimaryKey('PK_label_group_to_dataset', 'label_group_to_dataset', ["id"]);
        $this->alterColumn('label_group_to_dataset', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
        $this->createIndex('label_group_id', 'label_group_to_dataset', ["label_group_id"], false);
        $this->createIndex('dataset_id', 'label_group_to_dataset', ["dataset_id"], false);

    }

    public function down()
    {
        $this->dropTable("label_group_to_dataset");
    }

}