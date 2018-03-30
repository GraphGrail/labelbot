<?php

namespace frontend\controllers;

use common\components\EthereumGateway;
use common\models\Dataset;
use common\models\LabelGroup;
use common\models\Task;
use common\models\BlockchainCallback;
use common\domain\ethereum\Address;
use common\domain\ethereum\Contract;
use yii\filters\AccessControl;
use Yii;
use yii\web\NotFoundHttpException;

class TaskController extends \yii\web\Controller
{
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Shows user's Tasks
     */
    public function actionIndex()
    {
        $tasks = Task::find()
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();
        return $this->render('index', [
            'tasks' => $tasks
        ]);
    }

    /**
     *  Creates New Task
     */
    public function actionNew()
    {
        $model = new Task();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->status = Task::STATUS_CONTRACT_NOT_DEPLOYED;
            if ($model->save()) {
                $this->redirect($model->id . '/smart-contract');
            }
        }

        $datasets = Dataset::find()
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $labelGroups = LabelGroup::find()
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('new', [
            'model' => $model,
            'datasets' => $datasets,
            'labelGroups' => $labelGroups,
        ]);
    }

    /**
     * Creates smartcontract for Task
     * @param int $id Task id
     */
    public function actionSmartContract($id)
    {
        $blockchain  = new EthereumGateway;
        $task = Task::findOne($id);
        // Checks is task exists and belongs to user
        if ($task === null || $task->user_id !== Yii::$app->user->identity->id) {
            throw new \Exception("Can't find Task");
        }

        // Contract deployment
        $contractCanBeDeployed = $task->status === Task::STATUS_CONTRACT_NOT_DEPLOYED 
                              || $task->status === Task::STATUS_CONTRACT_DEPLOYMENT_ERROR;

        if ($contractCanBeDeployed && Yii::$app->request->isPost) {
            $clientAddress = new Address(Yii::$app->request->post()['address']);
            $task->deployContract($blockchain, $clientAddress);
        }

        // Contract activation payment
        if ($task->status === Task::STATUS_CONTRACT_NEW_NEED_TOKENS && Yii::$app->request->isPost) {
            // We need to check that contract tokenBalance really >= requiredInitialTokenBalance
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
            if ($contractStatus->tokenBalance >= $contractStatus->requiredInitialTokenBalance) {
                $task->status = Task::STATUS_CONTRACT_NEW;
                $task->save();
            }
        }

        // Contract activation
        if ($task->status === Task::STATUS_CONTRACT_NEW && Yii::$app->request->isPost) {
            // We need to check that contract is active
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
            if ($contractStatus->state === 'ACTIVE') {
                $task->status = Task::STATUS_CONTRACT_ACTIVE;
                $task->save();
            }
        }



        $views = [
            Task::STATUS_CONTRACT_NOT_DEPLOYED       => 'smartContract_deployment',
            Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS => 'smartContract_deploymentProcess',
            Task::STATUS_CONTRACT_NEW_NEED_TOKENS    => 'smartContract_sendTokens',
            Task::STATUS_CONTRACT_NEW                => 'smartContract_activation',
        ];
        return $this->render($views[$task->status] ?: 'smartContract', [
            'task' => $task
        ]);
    }


    /**
     * Creates smartcontract for Task
     * @param int $id Task id
     */
    public function actionSendTokens($id)
    {
        $blockchain  = new EthereumGateway;
        $task = Task::findOne($id);

        // Check is task exists and belongs to user
        if ($task === null || $task->user_id !== Yii::$app->user->identity->id) {
            throw new \Exception("Can't find Task");
        }

        $contractNotDeployed = $task->status === Task::STATUS_CONTRACT_NOT_DEPLOYED 
                            || $task->status === Task::STATUS_CONTRACT_DEPLOYMENT_ERROR;

        return $this->render('sendTokens', [
            'task' => $task
        ]);
    }


    /**
     * Credits users
     */
    public function actionGetCredit($id, $address)
    {
        $blockchain = new EthereumGateway;
        $walletAddress = new Address($address);

        // TODO: Check is balance really low

        $tokenContractAddress = Yii::$app->params['tokenContractAddress']; 

        $payload = [
            'tokenContractAddress' => (string) $tokenContractAddress,
            'recepientAddress'     => (string) $walletAddress,
            'etherValue' => (string) Yii::$app->params['creditEtherValue'],
            'tokenValue' => (string) Yii::$app->params['creditTokenValue']
        ];

        $callback_id = $blockchain->creditAccount($payload);

        $callback = new BlockchainCallback();
        $callback->type = BlockchainCallback::CREDIT_ACCOUNT;
        $callback->callback_id = $callback_id;
        $callback->params = json_encode($payload);
        
        if (!$callback->save()) {
            throw new \Exception("Can't save Callback after creditAcount() was called");
        }

        return $this->redirect("/task/$id/smart-contract");
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if (!$model = Task::findOne($id)) {
            throw new NotFoundHttpException(sprintf('Task with id `%s` not found', $id));
        }
        $model->delete();
        return $this->asJson([
            'success' => $model->deleted,
        ]);
    }

}
