<?php

class m180331_000001_castDb_work_item_columns_added_to_task extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('task', 'work_item_size', "int(10) unsigned NOT NULL AFTER description");
        $this->addColumn('task', 'total_work_items', "int(10) unsigned NOT NULL AFTER work_item_size");
        $this->alterColumn('task', 'contract_address', "varchar(42) NULL DEFAULT NULL AFTER total_work_items");
    }

    public function down()
    {
        $this->dropColumn('task', 'work_item_size');
        $this->dropColumn('task', 'total_work_items');
        $this->alterColumn('task', 'contract_address', "varchar(42) NULL DEFAULT NULL AFTER description");
    }

}