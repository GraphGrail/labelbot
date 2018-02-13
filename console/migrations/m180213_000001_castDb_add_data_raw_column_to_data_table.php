<?php

class m180213_000001_castDb_add_data_raw_column_to_data_table extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('data', 'data_raw', "text NOT NULL AFTER data");
    }

    public function down()
    {
        $this->dropColumn('data', 'data_raw');
    }

}