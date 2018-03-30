<?php

namespace console\controllers;

use yii\console\ExitCode;

class BlockchainController extends \yii\console\Controller
{

    public function actionUpdateCompletedWork()
    {
        try {
            echo 'OK';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return ExitCode::OK;
    }
}
