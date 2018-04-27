<?php

class m180427_000001_castDb extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('user', 'credited_at', "int(11) NULL DEFAULT NULL AFTER credits");
        $this->alterColumn('user', 'created_at', "int(11) NOT NULL AFTER credited_at");
    }

    public function down()
    {
        $this->dropColumn('user', 'credited_at');
        $this->alterColumn('user', 'created_at', "int(11) NOT NULL AFTER credits");
    }

}