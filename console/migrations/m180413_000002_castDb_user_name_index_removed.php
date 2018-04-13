<?php

class m180413_000002_castDb_user_name_index_removed extends \yii\db\Migration
{

    public function up()
    {
        $this->dropIndex('username', 'user');
    }

    public function down()
    {
        $this->createIndex('username', 'user', ["username"], true);
    }

}