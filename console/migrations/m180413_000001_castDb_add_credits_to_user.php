<?php

class m180413_000001_castDb_add_credits_to_user extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('user', 'credits', "tinyint(3) NOT NULL DEFAULT 0 AFTER status");
        $this->alterColumn('user', 'created_at', "int(11) NOT NULL AFTER credits");
    }

    public function down()
    {
        $this->dropColumn('user', 'credits');
        $this->alterColumn('user', 'created_at', "int(11) NOT NULL AFTER status");
    }

}