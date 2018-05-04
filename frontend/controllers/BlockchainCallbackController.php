<?php

namespace frontend\controllers;

use common\models\Task;
use common\models\BlockchainCallback;
use console\jobs\CreateWorksJob;
use yii\filters\AccessControl;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * BlockchainCallbackController
 * 
 * Action names for callback must be the same as Gateway API methods
 * names that returns callbacks
 */
class BlockchainCallbackController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    /**
     * Array of callback request data 
     */
    protected $data;
    /**
     * Array of Callback-object saved params 
     */
    protected $params;

	/**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                ],
            ],
        ];
    }


    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $this->data = Yii::$app->request->post();

        /** @var BlockchainCallback $callback */
        $callback = BlockchainCallback::find()
            ->where(['callback_id' => $this->data['taskId']])
            ->one();

        if ($callback === null) {
            throw new BadRequestHttpException("Invalid Callback id");
        }

        $callback->received = 1;
        $callback->success = $this->data['success'] ?: '0';
        $callback->error = json_encode($this->data['error']) ?: '';
        $callback->payload = json_encode($this->data['payload']) ?: '';

        if (!$callback->save()) {
            throw new BadRequestHttpException("Invalid Callback data");
        }

        $this->params = json_decode($callback->params, true);

        return parent::beforeAction($action);
    }


    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\ExitException
     */
    public function actionDeployContract()
    {
        $task = Task::findOne($this->params['task_id']);

        if ($task === null) {
            throw new NotFoundHttpException("Can't find Task");
        }

        if (!$this->data['success']) {
            // TODO: Handle errors

            $task->status = Task::STATUS_CONTRACT_DEPLOYMENT_ERROR;
            if (!$task->save()) {
                throw new ServerErrorHttpException(500, "Can't save Task");
            }

            Yii::$app->end();
        } 

        $task->contract_address = $this->data['payload']['contractAddress'];

        if (!$task->save()) {
            throw new ServerErrorHttpException("Can't save Task");
        }

        Yii::$app->queue->push(new CreateWorksJob([
            'task_id' => $task->id
        ]));

        Yii::$app->end();
    }

    /**
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     * @throws ServerErrorHttpException
     */
    public function actionUpdateCompletedWork()
    {
        $task = Task::findOne($this->params['task_id']);

        if ($task === null) {
            throw new NotFoundHttpException("Can't find Task");
        }
        
        if (!$this->data['payload']['success']) {
            // TODO: Handle errors

            Yii::$app->end();
        }

        if ($task->status === Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE) {
            $task->status = Task::STATUS_CONTRACT_ACTIVE_PAUSED;
            if (!$task->save()) {
                throw new ServerErrorHttpException("Can't save Task");
            } 
        }

        Yii::$app->end();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionForceFinalize()
    {
        // Here we do nothing for now.
        Yii::$app->end();
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionCreditAccount()
    {
        // Here we do nothing for now.
        Yii::$app->end();
    }


}
