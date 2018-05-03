<?php

namespace frontend\controllers;

use common\components\EthereumGateway;
use common\models\Data;
use common\models\DataLabel;
use common\models\Dataset;
use common\models\Label;
use common\models\LabelGroup;
use common\models\Moderator;
use common\models\Task;
use common\models\BlockchainCallback;
use common\domain\ethereum\Address;
use common\models\User;
use common\models\view\PreviewScoreWorkView;
use common\models\view\TaskDetailView;
use common\models\view\TaskScoreWorkView;
use common\models\WorkItem;
use console\jobs\SynchronizeTaskStatusJob;
use frontend\models\SendScoreWorkForm;
use yii\filters\AccessControl;
use Yii;
use yii\log\Logger;
use yii\web\HttpException;
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
                'class' => AccessControl::class,
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
            if ($labelGroup === null) throw new \Exception("Incorrect labelGroup_id");
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
     * Creation and activation of smart contract for Task
     * @param int $id Task id
     * @return string
     * @throws \Exception
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

        // Contract reactivation payment
        if ($task->status === Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS && Yii::$app->request->isPost) {
            // We need to check that contract tokenBalance enough for workItemsLeft will be payed
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
            if ($contractStatus->workItemsBalance >= $contractStatus->workItemsLeft) {
                $task->status = Task::STATUS_CONTRACT_ACTIVE;
                $task->save();
            }
        }

        // Contract needTokens views
        if ($task->status === Task::STATUS_CONTRACT_NEW_NEED_TOKENS || $task->status === Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS) {
            // We need to check that contract tokenBalance enough for workItemsLeft will be payed
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
            $tokensValue = bcmul(($contractStatus->workItemsLeft - $contractStatus->workItemsBalance), $contractStatus->workItemPrice);
        }       

        $views = [
            Task::STATUS_CONTRACT_NOT_DEPLOYED       => 'smartContract_deployment',
            Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS => 'smartContract_deploymentProcess',
            Task::STATUS_CONTRACT_NEW_NEED_TOKENS    => 'smartContract_sendTokens',
            Task::STATUS_CONTRACT_NEW                => 'smartContract_activation',
            Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS => 'smartContract_sendTokens',
        ];
        return $this->render(array_key_exists($task->status, $views) ? $views[$task->status]: 'smartContract', [
            'task' => $task,
            'tokensValue' => $tokensValue ?? 0
        ]);
    }


    public function actionPause($id)
    {
        $task = Task::find()
            ->where(['id'=>$id])
            ->ownedByUser() // task must belongs to user
            ->one();

        if ($task === null) {
            throw new HttpException(404, "Can't find Task");
        }

        if ($task->status !== Task::STATUS_CONTRACT_ACTIVE) {
            // TODO: remove that
            throw new HttpException(500, "Task must be active for pause.");
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
     * @param $id
     * @throws \Exception
     */
    public function actionRelease($id)
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

        if ($task->status !== Task::STATUS_CONTRACT_ACTIVE_PAUSED) {
            // TODO: remove that
            throw new \Exception("Task must be paused for activation.");
        }

        try {
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
        } catch (\Exception $e) {
             throw new \Exception("Cant get Task contract status."); 
        }


        $workItemsInBlockchain = json_decode(json_encode($contractStatus->workers), true);
        $workItemsInDb = [];

        $approvedWorks = (new \yii\db\Query)
            ->select(['moderator_address', 'COUNT(moderator_address) AS count'])
            ->from(WorkItem::tableName())
            ->where(['task_id'=>$task->id])
            ->andWhere(['status'=>WorkItem::STATUS_APPROVED])
            ->groupBy(['moderator_address'])
            ->all();

        foreach ($approvedWorks as $work) {
            $workItemsInDb[$work['moderator_address']]['approvedItems'] = (int) $work['count'];
        }

        $declinedWorks = (new \yii\db\Query)
            ->select(['moderator_address', 'COUNT(moderator_address) AS count'])
            ->from(WorkItem::tableName())
            ->where(['task_id'=>$task->id])
            ->andWhere(['status'=>WorkItem::STATUS_DECLINED])
            ->groupBy(['moderator_address'])
            ->all();

        foreach ($declinedWorks as $work) {
            $workItemsInDb[$work['moderator_address']]['declinedItems'] = (int) $work['count'];
        }

        $approvedWorksToUpdate = [];
        $declinedWorksToUpdate = [];
        // Find number of workItems that we must update in db
        foreach ($workItemsInBlockchain as $address => $workItems) {
            if (!array_key_exists($address, $workItemsInDb)) {
                $workItemsInDb[$address] = [];
            }
            $addressInDb = $workItemsInDb[$address];
            $addressInDb = $this->initResultItemsData($addressInDb);
            $workItems = $this->initResultItemsData($workItems);

            $numOfApprovedInDb = $addressInDb['approvedItems'];
            $numOfDeclinedInDb = $addressInDb['declinedItems'];

            if ($numOfApprovedInDb < $workItems['approvedItems']) {
                $approvedWorksToUpdate[$address] = $workItems['approvedItems'] - $numOfApprovedInDb;
            }
            if ($addressInDb['declinedItems'] < $workItems['declinedItems']) {
                $declinedWorksToUpdate[$address] = $workItems['declinedItems'] - $numOfDeclinedInDb;
            }
        }

        foreach ($approvedWorksToUpdate as $address => $num) {
            $moderator = Moderator::findOne(['eth_addr' => $address]);
            if ($moderator === null) continue; // TODO: Log sync error
            $task->approveWorkItems($moderator, $num);
        }

        foreach ($declinedWorksToUpdate as $address => $num) {
            $moderator = Moderator::findOne(['eth_addr' => $address]);
            if ($moderator === null) continue; // TODO: Log sync error
            $task->declineWorkItems($moderator, $num);
        }

        $task->status = Task::STATUS_CONTRACT_ACTIVE;

        if ($contractStatus->workItemsLeft > 0 && $contractStatus->workItemsBalance === 0) {
            $task->status = Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS;
        }

        if ($contractStatus->workItemsLeft === 0 && $contractStatus->canFinalize === true) {
            $task->status = Task::STATUS_CONTRACT_ACTIVE_COMPLETED;
        }

        $task->save();
        $this->redirect('view');
    }


    /**
     * Moderators' work scoring
     * @param int $id Task id
     * @return string|\yii\web\Response
     * @throws \Exception
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

        if ($task->status === Task::STATUS_CONTRACT_ACTIVE_PAUSED && Yii::$app->request->isPost) {
            return $this->redirect('release');
        }

        if ($task->status === Task::STATUS_CONTRACT_ACTIVE) {
            return $this->redirect(['pause', 'id' => $id]);
        }

        if ($task->status === Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE) {
            return $this->render('scoreWork_waitingPause', [
                'task' => $task
            ]);
        }

        if ($task->status === Task::STATUS_CONTRACT_FINALIZED) {
            return $this->redirect('/task/' . $task->id);
        }

        if ($task->status !== Task::STATUS_CONTRACT_ACTIVE_PAUSED && $task->status !== Task::STATUS_CONTRACT_ACTIVE_COMPLETED) {
            throw new HttpException(500, "Task must be paused or completed for scoring.");
        }

        try {
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
        } catch (\Exception $e) {
            $contractStatus = new \StdClass();
            $contractStatus->workers = [];
        }

        $view = new TaskScoreWorkView($task);
        $view->setContractStatus($contractStatus);

        return $this->render('scoreWork', [
            'task' => $task,
            'contractStatus' => $contractStatus,
            'sendingForm' => new SendScoreWorkForm(),
            'view' => $view,
        ]);
    }


    /**
     * Credits users
     * @param string $address
     * @return array
     * @throws \Exception
     */
    public function actionGetCredit($address)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user = User::findOne(['id'=>Yii::$app->user->id]);
        $time = time();

        if (!$user->credits) {
            return [
                'error'=>true,
                'error_code' => 'NO_CREDITS',
                'error_text' => "You already use all your credits."
            ];
        }

        if ($user->credited_at > ($time - Yii::$app->params['creditOnceAt'])) {
            return [
                'error'=>true,
                'error_code' => 'CREDIT_DAY_LIMIT',
                'error_text' => "You already get your credit for today. If you don't receive it yet, please wait some more time."
            ];
        }

        $blockchain = new EthereumGateway;
        $walletAddress = new Address($address);
        $tokenContractAddress = new Address(Yii::$app->params['tokenContractAddress']);

        $systemWalletAddress = $blockchain->walletAddress();
        $systemBalance = $blockchain->checkBalances($systemWalletAddress, $tokenContractAddress);

        if (bccomp(Yii::$app->params['creditTokenValue'], $systemBalance->token) === 1) {
            return [
                'error'=>true,
                'error_code' => 'NO_TOKEN_IN_SERVICE',
                'error_text' => "At the moment, credit feature is not available."
            ];
        }

        if (bccomp(Yii::$app->params['creditEtherValue'], $systemBalance->ether) === 1) {
            //TODO: replace this dirty hack
            if (!strpos($systemBalance->ether, 'e+')) {
                return [
                    'error'=>true,
                    'error_code' => 'NO_ETHER_IN_SERVICE',
                    'error_text' => "At the moment, credit feature is not available."
                ];
            }
        }

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

        $user->updateCounters(['credits'=> -1]);
        $user->credited_at = $time;
        $user->save();

        return [
            'error'=>false,
            'error_code' => null,
            'error_text' => null
        ];
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
    public function actionPreviewWork($id, $addr, $limit=10)
    {
        /** @var Task $task */
        if (!$task = Task::findOne($id)) {
            throw new NotFoundHttpException(sprintf('Task with id `%s` not found', $id));
        }

        /** @var Moderator $moderator */
        if (!$moderator = Moderator::find()->where(['eth_addr' => $addr])->one()) {
            throw new NotFoundHttpException(sprintf('Moderator with address `%s` not found', $addr));
        }

        $currentWorkItem = WorkItem::find()
            ->where(['task_id' => $task->id])
            ->andWhere('[[status]] = ' . WorkItem::STATUS_READY)
            ->andWhere('[[moderator_id]] = ' . $moderator->id)
            ->one();

        $list = array_slice($currentWorkItem->dataLabels, 0, 9);

        return $this->asJson([
            'list' => array_map(function (DataLabel $dataLabel) {
                return (new PreviewScoreWorkView($dataLabel))->toArray();
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
                    ->getWorkItems()
                    ->andWhere('[[status]] = ' . WorkItem::STATUS_APPROVED)
                    ->count()
            )
            ->setFullCount($task->total_work_items)
        ;

        $moderatorCountAssignedLabels = $this->getModeratorCountDataLabels($task);

        foreach ($moderatorCountAssignedLabels as $moderatorAddress =>  $moderatorCountAssignedLabel) {
            $view->addModeratorAssignedCount($moderatorAddress, $moderatorCountAssignedLabel);
        }

        return $this->render('detail', [
            'view' => $view,
            'task' => $task,
            'contractStatus' => $contractStatus,
        ]);
    }

    /**
     * @param $id
     * @return \yii\console\Response|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionDownloadResult($id)
    {
        /** @var Task $task */
        if (!$task = Task::findOne($id)) {
            throw new NotFoundHttpException(sprintf('Task with id `%s` not found', $id));
        }
        if ($task->user_id != Yii::$app->user->identity->id) {
            throw new NotFoundHttpException(sprintf('Task with id `%s` not found', $id));
        }
        if (!$task->isFinalized()) {
            throw new NotFoundHttpException(sprintf('Task with id `%s` not finalized', $id));
        }

        if (!$name = $task->result_file ?: $this->createCsvFile($task)) {
            Yii::$app->end();
        }
        $bucket = $this->getResultFileBucket();
        try {
            return Yii::$app->response->sendFile($bucket->getFullFileName($name), $name);
        } catch (\Exception $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
        Yii::$app->end();
    }

    public function actionSyncStatus($id)
    {
        Yii::$app->queue->push(new SynchronizeTaskStatusJob([
            'taskId' => $id,
        ]));
        return $this->asJson(['success' => true]);
    }

    protected function createCsvFile(Task $task)
    {
        try {
            $approvedWorkItems = $task->getWorkItems()
                ->andWhere(['status' => WorkItem::STATUS_APPROVED])
                ->all();

            /** @var \yii2tech\filestorage\local\Storage $fileStorage */
            $bucket = $this->getResultFileBucket();
            $resource = $bucket->openFile($this->createTaskResultFileName($task), 'w');

            foreach ($approvedWorkItems as $workItem) {
                foreach ($workItem->dataLabels as $dataLabel) {

                    /** @var Label $label */
                    if (!$label = $dataLabel->getLabel()->one()) {
                        continue;
                    }
                    /** @var Data $data */
                    if (!$data = $dataLabel->getData()->one()) {
                        continue;
                    }

                    $path = $label->buildPath();
                    array_unshift($path, $data->data);
                    fputcsv($resource, $path, ',');
                }
            }

            fclose($resource);

            $task->result_file = $this->createTaskResultFileName($task);
            $task->save(false, ['result_file']);

            return $task->result_file;
        } catch (\Exception $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
        return false;
    }

    private function createTaskResultFileName(Task $task)
    {
        return sprintf('%s_task_result.csv', $task->id);
    }

    /**
     * @param Task $task
     * @return array
     */
    private function getModeratorCountDataLabels(Task $task): array
    {
        $inHands = $task
            ->getWorkItems()
            ->where(['IN', 'status', [WorkItem::STATUS_IN_HAND, WorkItem::STATUS_READY]])
            ->all();

        $counts = [];
        foreach ($inHands as $workItem) {
            $address = $workItem->getModerator()->one()->eth_addr;
            if (!array_key_exists($address, $counts)) {
                $counts[$address] = 0;
            }

            $readyNumber = $workItem->getDataLabels()
                ->where(['status' => DataLabel::STATUS_READY])
                ->count();

            $counts[$address] += $readyNumber;
        }
        return $counts;
    }

    /**
     * @return \yii2tech\filestorage\local\Bucket
     */
    protected function getResultFileBucket(): \yii2tech\filestorage\local\Bucket
    {
        /** @var \yii2tech\filestorage\local\Storage $fileStorage */
        $fileStorage = Yii::$app->fileStorage;
        return $fileStorage->getBucket('result');
    }

    /**
     * @param $array
     * @return mixed
     */
    protected function initResultItemsData($array)
    {
        if (!array_key_exists('approvedItems', $array)) {
            $array['approvedItems'] = 0;
        }
        if (!array_key_exists('declinedItems', $array)) {
            $array['declinedItems'] = 0;
        }
        if (!array_key_exists('totalItems', $array)) {
            $array['totalItems'] = 0;
        }
        return $array;
    }

}
