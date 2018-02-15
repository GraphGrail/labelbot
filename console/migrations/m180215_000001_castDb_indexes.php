<?php

class m180215_000001_castDb_indexes extends \yii\db\Migration
{

    public function up()
    {
        $this->createIndex('label_id', 'assigned_label', ["label_id"], false);
        $this->createIndex('tg_id', 'moderator', ["tg_id"], false);
    }

    public function down()
    {
        $this->dropIndex('label_id', 'assigned_label');
        $this->dropIndex('tg_id', 'moderator');
    }

}