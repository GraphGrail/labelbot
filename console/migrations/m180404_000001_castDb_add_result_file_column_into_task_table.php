<?php

class m180404_000001_castDb_add_result_file_column_into_task_table extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('task', 'result_file', "text NULL DEFAULT NULL AFTER delivering_job_id");
    }

    public function down()
    {
        $this->dropColumn('task', 'result_file');
    }

}