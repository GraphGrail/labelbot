<?php

class m180315_000001_castDb_data_table_changes extends \yii\db\Migration
{

    public function up()
    {
        $this->alterColumn('data', 'data_raw', "text NULL DEFAULT NULL AFTER data");
        $this->alterColumn('data', 'id', "int(10) unsigned NOT NULL AUTO_INCREMENT");
    }

    public function down()
    {
        $this->alterColumn('data', 'id', "int(10) unsigned NOT NULL");
        $this->alterColumn('data', 'data_raw', "text NOT NULL AFTER data");
    }

}