<?php

namespace frontend\controllers;

use common\components\EthereumGateway;
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

        return $this->render('new', ['model' => $model]);
    }

    /**
     * Creates smartcontract for Task
     * @param int $id Task id
     */
    public function actionSmartContract($id)
    {
        $task = Task::findOne($id);

        // Check is task exists and belongs to user
        if ($task === null || $task->user_id !== Yii::$app->user->identity->id) {
            throw new \Exception("Can't find Task");
        }        

        if (Yii::$app->request->isPost) {
            // Commented for Dev
    /*        if ($task->status !== Task::STATUS_CONTRACT_NOT_DEPLOYED || $task->status !== Task::STATUS_CONTRACT_DEPLOYMENT_ERROR ) {
                throw new \Exception("Contract for this Task already was created");
            }*/

            $blockchain  = new EthereumGateway;
            $client_addr = new Address(Yii::$app->request->post()['address']);

            // Check that client have ethereum and tokens, if not, redirect to credit invitation
/*            $balance = $blockchain->checkBalances($client_addr);
            if ($balance->ether === 0 || $balance->token === 0) {
                Yii::$app->session->setFlash('credit-invitation', '');
                $this->refresh();
            }*/

            $contract    = new Contract($client_addr, 100); // TODO: real jobs number
            $callback_id = $blockchain->deployContract($contract);
            
            $callback_params = [
                'task_id' => $task->id
            ];

            $callback = new BlockchainCallback();
            $callback->type = BlockchainCallback::DEPLOY_CONTRACT;
            $callback->callback_id = $callback_id;
            $callback->params = json_encode($callback_params);
            
            if (!$callback->save()) {
                throw new \Exception("Can't save Callback after deployContract() was called");
            }

            $task->contract = json_encode($contract);

            $task->status = Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS;
            if (!$task->save()) {
                throw new \Exception("Can't update Task");
            }

        }

        switch ($task->status) {
            case Task::STATUS_CONTRACT_NOT_DEPLOYED:
                $view = 'smartContract_notDeployed';
                break;
            case Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS:
                $view = 'smartContract_deploymentProcess';
                break;
            case Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS:
                $view = 'smartContract_activation';
                break;
            default:
                $view = 'smartContract';
                break;
        }

        return $this->render($view, [
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
