<?php

class m180206_000001_castDb_create_index_moderator_table extends \yii\db\Migration
{

    public function up()
    {
        $this->createIndex('auth_token', 'moderator', ["auth_token"], true);
    }

    public function down()
    {
        $this->dropIndex('auth_token', 'moderator');
    }

}