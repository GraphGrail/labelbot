<?php

class m180420_000001_castDb_rbac extends \yii\db\Migration
{

    public function up()
    {
        $this->dropTable("auth_assignment");
        $this->dropTable("auth_item");
        $this->dropTable("auth_item_child");
        $this->dropTable("auth_rule");
    }

    public function down()
    {
        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_unicode_ci";

        $this->createTable('auth_assignment', [
            'item_name' => "varchar(64) NOT NULL", 
            'user_id' => "varchar(64) NOT NULL", 
            'created_at' => "int(11) NULL DEFAULT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_auth_assignment', 'auth_assignment', ["item_name","user_id"]);

        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_unicode_ci";

        $this->createTable('auth_item', [
            'name' => "varchar(64) NOT NULL", 
            'type' => "smallint(6) NOT NULL", 
            'description' => "text NULL DEFAULT NULL", 
            'rule_name' => "varchar(64) NULL DEFAULT NULL", 
            'data' => "blob NULL DEFAULT NULL", 
            'created_at' => "int(11) NULL DEFAULT NULL", 
            'updated_at' => "int(11) NULL DEFAULT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_auth_item', 'auth_item', ["name"]);
        $this->createIndex('rule_name', 'auth_item', ["rule_name"], false);
        $this->createIndex('idx-auth_item-type', 'auth_item', ["type"], false);

        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_unicode_ci";

        $this->createTable('auth_item_child', [
            'parent' => "varchar(64) NOT NULL", 
            'child' => "varchar(64) NOT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_auth_item_child', 'auth_item_child', ["parent","child"]);
        $this->createIndex('child', 'auth_item_child', ["child"], false);

        $tableOptions = 'ENGINE=InnoDB' . PHP_EOL . "COLLATE utf8_unicode_ci";

        $this->createTable('auth_rule', [
            'name' => "varchar(64) NOT NULL", 
            'data' => "blob NULL DEFAULT NULL", 
            'created_at' => "int(11) NULL DEFAULT NULL", 
            'updated_at' => "int(11) NULL DEFAULT NULL", 
        ],$tableOptions);

        $this->addPrimaryKey('PK_auth_rule', 'auth_rule', ["name"]);

        $this->addForeignKey('auth_assignment_ibfk_1', 'auth_assignment','item_name', 'auth_item', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('auth_item_ibfk_1', 'auth_item','rule_name', 'auth_rule', 'name', 'SET NULL', 'CASCADE');
        $this->addForeignKey('auth_item_child_ibfk_1', 'auth_item_child','parent', 'auth_item', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('auth_item_child_ibfk_2', 'auth_item_child','child', 'auth_item', 'name', 'CASCADE', 'CASCADE');
    }

}