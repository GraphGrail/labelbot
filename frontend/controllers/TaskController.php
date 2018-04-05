<?php

namespace frontend\controllers;

use common\components\EthereumGateway;
use common\models\AssignedLabel;
use common\models\Data;
use common\models\Dataset;
use common\models\Label;
use common\models\LabelGroup;
use common\models\Moderator;
use common\models\Task;
use common\models\BlockchainCallback;
use common\domain\ethereum\Address;
use common\domain\ethereum\Contract;
use common\models\view\ActionView;
use common\models\view\PreviewScoreWorkView;
use common\models\view\TaskDetailView;
use common\models\view\TaskScoreWorkView;
use console\jobs\SynchronizeTaskStatusJob;
use frontend\models\SendScoreWorkForm;
use yii\filters\AccessControl;
use Yii;
use yii\helpers\Url;
use yii\log\Logger;
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
            throw new \Exception("Task must be paused for activtion.");            
        }

        try {
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
        } catch (\Exception $e) {
             throw new \Exception("Cant get Task contract status."); 
        }


        $workItemsInBlockchain = json_decode(json_encode($contractStatus->workers), true);
        $workItemsInDb = [];

        $approvedWorks = (new \yii\db\Query)
            ->select(['moderator_id', 'moderator.eth_addr', 'COUNT(moderator_id) AS count'])
            ->from(AssignedLabel::tableName())
            ->join('JOIN', 'moderator', 'moderator.id = moderator_id')
            ->where(['task_id'=>$task->id])
            ->andWhere(['status'=>AssignedLabel::STATUS_APPROVED])
            ->groupBy(['moderator_id'])
            ->all();

        foreach ($approvedWorks as $work) {
            $approvedWorkItems = (int) ($work['count']/$task->work_item_size);
            // We don't get not completed workItems
            if ($approvedWorkItems === 0) continue;

            $workItemsInDb[$work['eth_addr']]['approvedItems'] = $approvedWorkItems;
        }

        $declinedWorks = (new \yii\db\Query)
            ->select(['moderator_id', 'moderator.eth_addr', 'COUNT(moderator_id) AS count'])
            ->from(AssignedLabel::tableName())
            ->join('JOIN', 'moderator', 'moderator.id = moderator_id')
            ->where(['task_id'=>$task->id])
            ->andWhere(['status'=>AssignedLabel::STATUS_DECLINED])
            ->groupBy(['moderator_id'])
            ->all();

        foreach ($declinedWorks as $work) {
            $declinedWorkItems = (int) $work['count'] / $task->work_item_size;
            // We don't get not completed workItems
            if ($declinedWorkItems === 0) continue;

            $workItemsInDb[$work['eth_addr']]['declinedItems'] = $declinedWorkItems;
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
            if ($moderator === null) continue;
            $task->approveWorkItems($moderator, $num);
        }

        foreach ($declinedWorksToUpdate as $address => $num) {
            $moderator = Moderator::findOne(['eth_addr' => $address]);
            if ($moderator === null) continue;
            $task->declineWorkItems($moderator, $num);
        }

        if ($contractStatus->workItemsLeft > 0 && $contractStatus->workItemsBalance === 0) {
            $task->status = Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS;
        }

        if ($contractStatus->workItemsLeft > 0 && $contractStatus->workItemsBalance > 0) {
            $task->status = Task::STATUS_CONTRACT_ACTIVE;
        }
        // TODO: use canFinalize if it works
        if ($contractStatus->workItemsLeft === 0) {
            $task->status = Task::STATUS_CONTRACT_ACTIVE_COMPLETED;
        }

        $task->save();
        $this->redirect('view');
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
            return $this->redirect(['pause', 'id' => $id]);
        }

        if ($task->status === Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE) {
            return $this->render('scoreWork_waitingPause', [
                'task' => $task
            ]);
        }

        if ($task->status !== Task::STATUS_CONTRACT_ACTIVE_PAUSED && $task->status !== Task::STATUS_CONTRACT_ACTIVE_COMPLETED) {
            // TODO: remove that
            throw new \Exception("Task must be paused or completed for scoring.");
        }

        try {
            $contractStatus = $blockchain->contractStatus($task->contractAddress());
        } catch (\Exception $e) {
            $contractStatus = new \StdClass();
            $contractStatus->workers = [];
        }

        return $this->render('scoreWork', [
            'task' => $task,
            'contractStatus' => $contractStatus,
            'sendingForm' => new SendScoreWorkForm(),
            'view' => new TaskScoreWorkView($task),
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

        $moderatorCountAssignedLabels = $this->getModeratorCountAssignedLabels($task);
        foreach ($moderatorCountAssignedLabels as $moderatorAddr =>  $moderatorCountAssignedLabel) {
            $view->addModeratorAssignedCount($moderatorAddr, $moderatorCountAssignedLabel);
        }

        return $this->render('detail', [
            'view' => $view,
            'task' => $task,
            'contractStatus' => $contractStatus,
        ]);
    }

    /**
     * @param $id
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
            /** @var AssignedLabel[] $models */
            $models = $task->getAssignedLabels()
                ->andWhere(['status' => AssignedLabel::STATUS_APPROVED])
                ->all();

            /** @var \yii2tech\filestorage\local\Storage $fileStorage */
            $bucket = $this->getResultFileBucket();

            $resource = $bucket->openFile($this->createTaskResultFileName($task), 'w');
            foreach ($models as $model) {
                /** @var Label $label */
                if (!$label = $model->getLabel()->one()) {
                    continue;
                }
                /** @var Data $data */
                if (!$data = $model->getData()->one()) {
                    continue;
                }
                $path = $label->buildPath();
                array_unshift($path, $data->data);
                fputcsv($resource, $path, ';');
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
        return sprintf('task_%s_result.csv', $task->id);
    }

    /**
     * @param Task $task
     * @param $contractStatus
     * @return array
     */
    private function getModeratorCountAssignedLabels(Task $task): array
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
            if (!array_key_exists($addr, $counts)) {
                $counts[$addr] = 0;
            }
            $counts[$addr]++;
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
