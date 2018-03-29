<?php

class m180329_000002_castDb_add_deleted_column_into_labels_group_table extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('label_group', 'deleted', "tinyint(1) NOT NULL DEFAULT 0 AFTER updated_at");
        $this->createIndex('deleted', 'label_group', 'deleted');
    }

    public function down()
    {
        $this->dropIndex('deleted', 'label_group');
        $this->dropColumn('label_group', 'deleted');
    }

}