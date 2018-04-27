<?php

use yii\db\Migration;

/**
 * Class m180420_141220_rbac_init
 */
class m180420_141220_rbac_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $auth = Yii::$app->authManager;

        $viewEntities = $auth->createPermission('viewEntities');
        $viewEntities->description = 'View Entities';
        $auth->add($viewEntities);

        $crudEntities = $auth->createPermission('crudEntities');
        $crudEntities->description = 'CRUD Entities';
        $auth->add($crudEntities);

        $moderator = $auth->createRole('moderator');
        $moderator->description = 'Moderator';
        $auth->add($moderator);
        $auth->addChild($moderator, $viewEntities);

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);
        $auth->addChild($admin, $moderator);
        $auth->addChild($admin, $crudEntities);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        Yii::$app->authManager->removeAll();
    }
}
