<?php

class m180329_000001_castDb_add_deleted_column_into_dataset_table extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('dataset', 'deleted', "tinyint(1) NOT NULL DEFAULT 0 AFTER updated_at");
        $this->createIndex('deleted', 'dataset', 'deleted');
    }

    public function down()
    {
        $this->dropIndex('deleted', 'dataset');
        $this->dropColumn('dataset', 'deleted');
    }

}