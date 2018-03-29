<?php

class m180329_000001_castDb_add_deleted_column_into_dataset_table extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('dataset', 'deleted', "tinyint(1) NOT NULL DEFAULT 0 AFTER updated_at");
    }

    public function down()
    {
        $this->dropColumn('dataset', 'deleted');
    }

}