<?php

class m180402_000001_castDb_assigned_label_status_added extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('assigned_label', 'status', "tinyint(3) unsigned NOT NULL AFTER moderator_id");
        $this->alterColumn('assigned_label', 'created_at', "int(11) NOT NULL AFTER status");
    }

    public function down()
    {
        $this->dropColumn('assigned_label', 'status');
        $this->alterColumn('assigned_label', 'created_at', "int(11) NOT NULL AFTER moderator_id");
    }

}