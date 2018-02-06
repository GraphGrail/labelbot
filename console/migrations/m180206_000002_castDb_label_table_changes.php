<?php

class m180206_000002_castDb_label_table_changes extends \yii\db\Migration
{

    public function up()
    {
        $this->dropIndex('label_group_id', 'label');
        $this->dropColumn('label', 'next_label_group_id');
        $this->addColumn('label', 'parent_label_id', "int(10) unsigned NULL DEFAULT NULL AFTER id");
        $this->alterColumn('label', 'text', "varchar(300) NULL DEFAULT NULL AFTER parent_label_id");
        $this->alterColumn('label', 'label_group_id', "int(10) unsigned NULL DEFAULT NULL AFTER text");
        $this->alterColumn('label', 'ordering', "int(11) NOT NULL DEFAULT 0 AFTER label_group_id");
        $this->createIndex('label_group_id', 'label', ["parent_label_id"], false);
    }

    public function down()
    {
        $this->dropIndex('label_group_id', 'label');
        $this->dropColumn('label', 'parent_label_id');
        $this->alterColumn('label', 'text', "varchar(300) NULL DEFAULT NULL AFTER label_group_id");
        $this->addColumn('label', 'next_label_group_id', "int(10) unsigned NULL DEFAULT NULL AFTER text");
        $this->alterColumn('label', 'label_group_id', "int(10) unsigned NULL DEFAULT NULL AFTER id");
        $this->alterColumn('label', 'ordering', "int(11) NOT NULL DEFAULT 0 AFTER next_label_group_id");
        $this->createIndex('label_group_id', 'label', ["label_group_id"], false);
    }

}