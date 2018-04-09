<?php

class m180409_000003_castDb_add_timestampts_columns_into_work_item_and_lock_tables extends \yii\db\Migration
{

    public function up()
    {
        $this->addColumn('lock_entity', 'created_at', "int(11) NULL DEFAULT NULL AFTER entityPk");
        $this->addColumn('lock_entity', 'updated_at', "int(11) NULL DEFAULT NULL AFTER created_at");
        $this->addColumn('work_item', 'created_at', "int(11) NULL DEFAULT NULL AFTER items");
        $this->addColumn('work_item', 'updated_at', "int(11) NULL DEFAULT NULL AFTER created_at");
    }

    public function down()
    {
        $this->dropColumn('lock_entity', 'created_at');
        $this->dropColumn('lock_entity', 'updated_at');
        $this->dropColumn('work_item', 'created_at');
        $this->dropColumn('work_item', 'updated_at');
    }

}