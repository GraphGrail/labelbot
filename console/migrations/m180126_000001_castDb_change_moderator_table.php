<?php

class m180126_000001_castDb_change_moderator_table extends \yii\db\Migration
{

    public function up()
    {
        $this->alterColumn('moderator', 'tg_id', "int(11) NULL DEFAULT NULL AFTER tg_chat_id");
    }

    public function down()
    {
        $this->alterColumn('moderator', 'tg_id', "int(11) NOT NULL AFTER tg_chat_id");
    }

}