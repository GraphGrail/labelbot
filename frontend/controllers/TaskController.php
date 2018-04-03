<?php

namespace frontend\controllers;

use common\components\EthereumGateway;
use common\models\AssignedLabel;
use common\models\Data;
use common\models\Dataset;
use common\models\LabelGroup;
use common\models\Moderator;
use common\models\Task;
use common\models\BlockchainCallback;
use common\domain\ethereum\Address;
use common\domain\ethereum\Contract;
use common\models\view\PreviewScoreWorkView;
use common\models\view\TaskDetailView;
use frontend\models\SendScoreWorkForm;
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
            ->ownedByUser()
            ->undeleted()
            ->orderBy(['created_at' => SORT_DESC])
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

            // We must verify dataset_id and label_group_id correctness
            $dataset = Dataset::find()
                ->where(['id' => $model->dataset_id])
                ->ownedByUser()
                ->ready()
                ->undeleted()
                ->one();
            if ($dataset === null) throw new \Exception("Incorrect dataset_id");

            $labelGroup = LabelGroup::find()
                ->where(['id' => $model->label_group_id])
                ->ownedByUser()
                ->undeleted()
                ->one();
            if ($labelGroup === null) $model->total_work_items;

            // We must save actual workItemSize on the moment of Task creation and use it later 
            // in operations belong to this Task, coz workItemSize in config can be modified.
            $model->work_item_size = Yii::$app->params['workItemSize'];
            $model->total_work_items = (int) ($dataset->dataCount/$model->work_item_size);

            $model->status = Task::STATUS_CONTRACT_NOT_DEPLOYED;
            if ($model->save()) {
                $this->redirect($model->id . '/smart-contract');
            }
        }

        $datasets = Dataset::find()
            ->ownedByUser()
            ->ready()
            ->undeleted()
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $labelGroups = LabelGroup::find()
            ->ownedByUser()
            ->undeleted()
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('new', [
            'model' => $model,
            'datasets' => $datasets,
            'labelGroups' => $labelGroups,
        ]);
    }

    /**
     * Creation and activation of smartcontract for Task
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
        return $this->render(array_key_exists($task->status, $views) ? $views[$task->status]: 'smartContract', [
            'task' => $task
        ]);
    }


    public function actionStop($id)
    {
        $task = Task::find()
            ->where(['id'=>$id])
            ->ownedByUser() // task must belongs to user
            ->one();

        if ($task === null) {
            throw new \Exception("Can't find Task");
        }

        if ($task->status !== Task::STATUS_CONTRACT_ACTIVE) {
            // TODO: remove that
            throw new \Exception("Task must be active for pause.");            
        }

        // Runs console command blockchain/update-completed-work for this task
        // Task status must be ACTIVE
        $c = new \console\controllers\BlockchainController(Yii::$app->controller->id, Yii::$app);
        $c->runAction('update-completed-work', ['taskId'=>$task->id]);

        $task->status = Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE;
        $task->save();

        $this->redirect('score-work');
    }


    /**
     * Moderators' work scoring
     * @param int $id Task id
     */
    public function actionScoreWork($id)
    {
        $blockchain  = new EthereumGateway;

        /** @var Task $task */
        $task = Task::find()
            ->where(['id'=>$id])
            ->ownedByUser() // task must belongs to user
            ->one();

        if ($task === null) {
            throw new \Exception("Can't find Task");
        }
        if ($task->status === Task::STATUS_CONTRACT_ACTIVE) {
            return $this->redirect(['stop', 'id' => $id]);
        }

        if ($task->status === Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE) {
            return $this->render('scoreWork_waitingPause', [
                'task' => $task
            ]);
        }

        if ($task->status !== Task::STATUS_CONTRACT_ACTIVE_PAUSED) {
            // TODO: remove that
            throw new \Exception("Task must be paused for scoring.");
        }
        //todo remove dev data
        try {
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
        } catch (\Exception $e) {
            $contractStatus = new \StdClass();
            $contractStatus->workers = [];
//            $contractStatus->workers = [
//                '0x13fb25c0e3c3a2c4bd84388cc1d36648f921e151'=>['totalItems'=>5,'approvedItems'=>2,'declinedItems'=>1],
//                '0x23fb25c0e3c3a2c4bd84388cc1d36648f921e152'=>['totalItems'=>2,'approvedItems'=>2,'declinedItems'=>0],
//                '0x33fb25c0e3c3a2c4bd84388cc1d36648f921e153'=>['totalItems'=>8,'approvedItems'=>2,'declinedItems'=>3]
//            ];
        }


        return $this->render('scoreWork', [
            'task' => $task,
            'contractStatus' => $contractStatus,
            'sendingForm' => new SendScoreWorkForm(),
        ]);

    }


    /**
     * Creates smartcontract for Task
     * @param int $id Task id
     */
    public function actionSendTokens($id)
    {
        $blockchain  = new EthereumGateway;

        $task = Task::find()
            ->where(['id'=>$id])
            ->ownedByUser() // task must belongs to user
            ->one();

        if ($task === null) {
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

    /**
     * @param $id
     * @param $addr
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionPreviewWork($id, $addr)
    {
        /** @var Task $task */
        if (!$task = Task::findOne($id)) {
            throw new NotFoundHttpException(sprintf('Task with id `%s` not found', $id));
        }

        /** @var Moderator $moderator */
        if (!$moderator = Moderator::find()->where(['eth_addr' => $addr])->one()) {
            throw new NotFoundHttpException(sprintf('Moderator with address `%s` not found', $addr));
        }

        $limit = 10;
        $list = $task
            ->getAssignedLabels()
            ->andWhere('[[status]] = ' . AssignedLabel::STATUS_READY)
            ->andWhere('[[moderator_id]] = ' . $moderator->id)
            ->addOrderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->all()
        ;

        return $this->asJson([
            'list' => array_map(function (AssignedLabel $assignedLabel) {
                return (new PreviewScoreWorkView($assignedLabel))->toArray();
            }, $list),
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        /** @var Task $task */
        if (!$task = Task::findOne($id)) {
            throw new NotFoundHttpException(sprintf('Task with id `%s` not found', $id));
        }
        $blockchain  = new EthereumGateway;
        try {
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
        } catch (\Exception $e) {
            $contractStatus = new \StdClass();
            $contractStatus->workers = [];
        }

        $view = new TaskDetailView($task);
        $view
            ->setContractStatus($contractStatus)
            ->setApprovedCount(
                $task
                ->getAssignedLabels()
                ->andWhere('[[status]] = ' . AssignedLabel::STATUS_APPROVED)
                ->count()
            )
            ->setFullCount(
                Data::find()
                ->where(['dataset_id' => $task->dataset_id])
                ->count()
            )
        ;

        $moderatorCountAssignedLabels = $this->getModeratorCountAssignedLabels($task, $contractStatus);
        foreach ($contractStatus->workers as $moderatorAddr => $worker) {
            if (!array_key_exists($moderatorAddr, $moderatorCountAssignedLabels)) {
                $moderatorCountAssignedLabels[$moderatorAddr] = 0;
            }
            $view->addModeratorAssignedCount($moderatorAddr, $moderatorCountAssignedLabels[$moderatorAddr]);
        }

        return $this->render('detail', [
            'view' => $view,
            'task' => $task,
            'contractStatus' => $contractStatus,
        ]);
    }

    /**
     * @param Task $task
     * @param $contractStatus
     * @return array
     */
    private function getModeratorCountAssignedLabels(Task $task, $contractStatus): array
    {
        /** @var AssignedLabel[] $assigned */
        $assigned = $task
            ->getAssignedLabels()
            ->andWhere('[[status]] = ' . AssignedLabel::STATUS_READY)
            ->all()
        ;
        $counts = [];
        foreach ($assigned as $assignedLabel) {
            $addr = $assignedLabel->getModerator()->one()->eth_addr;
            if (!array_key_exists($addr, $contractStatus->workers)) {
                continue;
            }
            if (!array_key_exists($addr, $counts)) {
                $counts[$addr] = 0;
            }
            $counts[$addr]++;
        }
        return $counts;
    }

}
