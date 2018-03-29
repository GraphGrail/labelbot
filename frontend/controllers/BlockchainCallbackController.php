<?php

namespace frontend\controllers;

use common\models\Task;
use common\models\BlockchainCallback;
use yii\web\Controller;
use yii\filters\AccessControl;
use Yii;

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
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                ],
            ],
        ];
    }


    public function beforeAction($action)
    {
        $this->data = Yii::$app->request->post();

        $callback = BlockchainCallback::find()
            ->where(['callback_id' => $this->data['taskId']])
            ->one();

        if ($callback === null) {
            throw new \Exception("Invalid Callback id");
        }

        $callback->received = 1;
        $callback->success = $this->data['success'] ?: '0';
        $callback->error = json_encode($this->data['error']) ?: '';
        $callback->payload = json_encode($this->data['payload']) ?: '';

        if (!$callback->save()) {
            throw new \Exception("Invalid Callback data");
        }

        $this->params = json_decode($callback->params, true);

        return parent::beforeAction($action);
    }

    // We don't handle requests to index
    //  public function actionIndex() {}

    public function actionDeployContract()
    {
        $task = Task::findOne($this->params['task_id']);

        if ($task === null) {
            throw new \Exception("Can't find Task");
        }

        if (!$this->data['success']) {
            // TODO: Handle errors

            $task->status = Task::STATUS_CONTRACT_DEPLOYMENT_ERROR;
            if (!$task->save()) {
                throw new \Exception("Can't save Task");
            }

            Yii::$app->end();
        } 

        $task->contract_address = $this->data['payload']['contractAddress'];
        $task->status = Task::STATUS_CONTRACT_NEW_NEED_TOKENS;

        if (!$task->save()) {
            throw new \Exception("Can't save Task");
        }

        Yii::$app->end();
    }

    public function actionUpdateCompletedWork()
    {

        Yii::$app->end();
    }

    public function actionForceFinalize()
    {

        Yii::$app->end();
    }

    public function actionCreditAccount()
    {
        // Here we do nothing for now.
        Yii::$app->end();
    }


}
