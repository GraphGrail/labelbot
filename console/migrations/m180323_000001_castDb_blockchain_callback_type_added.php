<?php

class m180323_000001_castDb_blockchain_callback_type_added extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('blockchain_callback', 'type', "tinyint(4) NOT NULL AFTER id");
        $this->alterColumn('blockchain_callback', 'params', "text NULL DEFAULT NULL AFTER type");
    }

    public function down()
    {
        $this->dropColumn('blockchain_callback', 'type');
        $this->alterColumn('blockchain_callback', 'params', "text NULL DEFAULT NULL AFTER id");
    }

}