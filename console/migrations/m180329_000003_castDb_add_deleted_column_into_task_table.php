<?php

class m180329_000003_castDb_add_deleted_column_into_task_table extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('task', 'deleted', "tinyint(1) NOT NULL DEFAULT 0 AFTER updated_at");
        $this->createIndex('deleted', 'task', 'deleted');
    }

    public function down()
    {
        $this->dropIndex('deleted', 'task');
        $this->dropColumn('task', 'deleted');
    }

}