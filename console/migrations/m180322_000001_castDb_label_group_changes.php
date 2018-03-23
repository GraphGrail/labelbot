<?php

class m180322_000001_castDb_label_group_changes extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('label_group', 'labels_tree', "text NOT NULL AFTER description");
        $this->alterColumn('label_group', 'status', "int(11) NOT NULL AFTER labels_tree");
    }

    public function down()
    {
        $this->dropColumn('label_group', 'labels_tree');
        $this->alterColumn('label_group', 'status', "int(11) NOT NULL AFTER description");
    }

}