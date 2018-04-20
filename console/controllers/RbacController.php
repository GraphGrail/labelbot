<?php
/**
 * Created by PhpStorm.
 * User: bytecrow
 * Date: 20.04.2018
 * Time: 17:19
 */

namespace console\controllers;

use common\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionAssign($role, $email)
    {
        $user = User::find()->where(['email' => $email])->one();
        if (!$user) {
            throw new InvalidArgumentException("There is no user with email \"$email\".");
        }

        $auth = Yii::$app->authManager;
        $roleObject = $auth->getRole($role);
        if (!$roleObject) {
            throw new InvalidArgumentException("There is no role \"$role\".");
        }

        try {
            $auth->assign($roleObject, $user->id);
        } catch (\Exception $e) {
            echo $e;
        }
    }
}
