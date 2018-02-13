<?php

class m180213_000002_castDb_assigned_label_table_changes extends \yii\db\Migration
{

    public function up()
    {
        $this->dropIndex('label_id', 'assigned_label');
        $this->addColumn('assigned_label', 'updated_at', "int(11) NOT NULL AFTER created_at");
        $this->alterColumn('assigned_label', 'label_id', "int(10) unsigned NULL DEFAULT NULL AFTER data_id");
    }

    public function down()
    {
        $this->dropColumn('assigned_label', 'updated_at');
        $this->alterColumn('assigned_label', 'label_id', "int(10) unsigned NOT NULL AFTER data_id");
        $this->createIndex('label_id', 'assigned_label', ["label_id"], false);
    }

}