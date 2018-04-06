<?php

class m180406_000001_castDb_assigned_label_changes extends \yii\db\Migration
{

    public function up()
    {
        $this->alterColumn('assigned_label', 'data_id', "int(10) unsigned NULL DEFAULT NULL AFTER task_id");
        $this->alterColumn('assigned_label', 'moderator_id', "int(10) unsigned NULL DEFAULT NULL AFTER label_id");
    }

    public function down()
    {
        $this->alterColumn('assigned_label', 'data_id', "int(10) unsigned NOT NULL AFTER task_id");
        $this->alterColumn('assigned_label', 'moderator_id', "int(10) unsigned NOT NULL AFTER label_id");
    }

}