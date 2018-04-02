<?php

class m180402_000002_castDb_moderator_current_task_added extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('moderator', 'current_task', "varchar(42) NULL DEFAULT NULL AFTER phone");
        $this->alterColumn('moderator', 'created_at', "int(11) NOT NULL AFTER current_task");
    }

    public function down()
    {
        $this->dropColumn('moderator', 'current_task');
        $this->alterColumn('moderator', 'created_at', "int(11) NOT NULL AFTER phone");
    }

}