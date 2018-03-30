<?php

class m180330_000001_castDb_add_delivering_job_id_column_into_task_table extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('task', 'delivering_job_id', "varchar(255) NOT NULL DEFAULT 0 AFTER deleted");
    }

    public function down()
    {
        $this->dropColumn('task', 'delivering_job_id');
    }

}