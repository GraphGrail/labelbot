<?php

class m180322_000003_castDb_some_changes extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('blockchain_callback', 'params', "text NULL DEFAULT NULL AFTER id");
        $this->alterColumn('blockchain_callback', 'callback_id', "varchar(32) NOT NULL AFTER params");
        $this->addColumn('task', 'name', "varchar(255) NOT NULL AFTER label_group_id");
        $this->alterColumn('task', 'description', "text NULL DEFAULT NULL AFTER name");
        $this->addColumn('task', 'contract_address', "varchar(42) NULL DEFAULT NULL AFTER description");
        $this->addColumn('task', 'contract', "text NULL DEFAULT NULL AFTER contract_address");
        $this->addColumn('task', 'status', "tinyint(3) unsigned NOT NULL AFTER contract");
        $this->alterColumn('task', 'created_at', "int(11) NOT NULL AFTER status");
        $this->alterColumn('task', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
    }

    public function down()
    {
        $this->dropColumn('blockchain_callback', 'params');
        $this->dropColumn('task', 'name');
        $this->dropColumn('task', 'contract_address');
        $this->dropColumn('task', 'contract');
        $this->dropColumn('task', 'status');
        $this->alterColumn('blockchain_callback', 'callback_id', "varchar(32) NOT NULL AFTER id");
        $this->alterColumn('task', 'id', "int(10) unsigned NOT NULL");
        $this->alterColumn('task', 'description', "varchar(255) NOT NULL AFTER label_group_id");
        $this->alterColumn('task', 'created_at', "int(11) NOT NULL AFTER description");
    }

}