<?php

class m180428_000001_castDb_work_item_status_added extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('work_item', 'status', "int(11) NULL DEFAULT NULL AFTER items");
        $this->alterColumn('work_item', 'created_at', "int(11) NULL DEFAULT NULL AFTER status");
    }

    public function down()
    {
        $this->dropColumn('work_item', 'status');
        $this->alterColumn('work_item', 'created_at', "int(11) NULL DEFAULT NULL AFTER items");
    }

}