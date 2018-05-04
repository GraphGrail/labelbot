<?php

namespace common\models;

use common\components\EthereumGateway;
use common\domain\ethereum\Address;
use common\domain\ethereum\Contract;
use common\interfaces\BlockchainGatewayInterface;
use Yii;
use yii\log\Logger;
use yii\web\HttpException;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $user_id
 * @property int $dataset_id
 * @property int $label_group_id
 * @property string $name
 * @property string $description
 * @property string $contract_address
 * @property string $contract
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property bool $deleted
 * @property string $delivering_job_id
 * @property int $work_item_size
 * @property int $total_work_items
 * @property string $result_file
 */
class Task extends ActiveRecord
{
    /**
     * Statuses
     */
    const STATUS_CONTRACT_NOT_DEPLOYED         = 10;
    const STATUS_CONTRACT_DEPLOYMENT_PROCESS   = 20;
    const STATUS_CONTRACT_DEPLOYMENT_ERROR     = 30;
    const STATUS_CONTRACT_NEW_NEED_TOKENS      = 40;
    const STATUS_CONTRACT_NEW                  = 50;
    const STATUS_CONTRACT_ACTIVE               = 60;
    const STATUS_CONTRACT_ACTIVE_NEED_TOKENS   = 70;
    const STATUS_CONTRACT_ACTIVE_WAITING_PAUSE = 80;
    const STATUS_CONTRACT_ACTIVE_PAUSED        = 90;
    const STATUS_CONTRACT_ACTIVE_COMPLETED     = 100;
    const STATUS_CONTRACT_FORCE_FINALIZING     = 110;
    const STATUS_CONTRACT_FINALIZED            = 120;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dataset_id', 'label_group_id', 'total_work_items', 'status','name'], 'required'],
            [['dataset_id', 'label_group_id', 'total_work_items', 'status'], 'integer'],
            [['description', 'contract'], 'string'],
            [['name', 'delivering_job_id'], 'string', 'max' => 255],
            [['contract_address'], 'string', 'max' => 42],
            [['total_work_items'], 'integer', 'min' => 1,  'tooSmall' => 'Very few data in dataset to create Task.'],
            [['deleted'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
            [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => null,
            ],
            'typecast' => [
                'class' => \yii\behaviors\AttributeTypecastBehavior::class,
                'typecastAfterFind' => true,
            ],
            'deletedAttribute' => [
                'class' => behavior\DeletedAttributeBehavior::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'dataset_id' => Yii::t('app', 'Dataset ID'),
            'label_group_id' => Yii::t('app', 'Label Group ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'work_item_size' => Yii::t('app', 'Work item size'),
            'total_work_items' => Yii::t('app', 'Total work items'),
            'contract_address' => Yii::t('app', 'Contract Address'),
            'contract' => Yii::t('app', 'Contract'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

    public function getDataset()
    {
        return $this->hasOne(Dataset::class, ['id' => 'dataset_id'])->one();
    }

    public function getLabelGroup()
    {
        return $this->hasOne(LabelGroup::class, ['id' => 'label_group_id'])->one();
    }

    public function getWorkItems()
    {
        return $this->hasMany(WorkItem::class, ['task_id' => 'id']);
    }

    /**
     * @param BlockchainGatewayInterface $blockchain
     * @param Address $clientAddress
     * @throws \Exception
     */
    public function deployContract(BlockchainGatewayInterface $blockchain, Address $clientAddress)
    {
        $contract    = new Contract($clientAddress, $this->total_work_items);
        $callback_id = $blockchain->deployContract($contract);

        $callback_params = [
            'task_id' => $this->id
        ];

        $callback = new BlockchainCallback();
        $callback->type = BlockchainCallback::DEPLOY_CONTRACT;
        $callback->callback_id = $callback_id;
        $callback->params = json_encode($callback_params);

        if (!$callback->save()) {
            throw new HttpException(500, "Can't save Callback after deployContract() was called");
        }

        $this->contract = json_encode($contract);

        $this->status = Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS;
        if (!$this->save()) {
            throw new HttpException(500, "Can't update Task");
        }
    }

    public function contractAddress() : ?Address
    {
        return $this->contract_address ? new Address($this->contract_address) : null;
    }


    /**
     * @param Moderator $moderator
     * @return DataLabel|null
     */
    public function getDataForLabelAssignment(Moderator $moderator) : ?DataLabel
    {
        $currentWorkItem = WorkItem::find()
            ->where(['task_id' => $this->id])
            ->andWhere(['moderator_id' => $moderator->id])
            ->andWhere(['OR', ['status' => WorkItem::STATUS_IN_HAND], ['status' => WorkItem::STATUS_READY]])
            ->one();

        if ($currentWorkItem === null) {
            $blockchain      = new EthereumGateway;
            $contractAddress = new Address($this->contract_address);

            try {
                $contractStatus  = $blockchain->contractStatus($contractAddress);
            } catch (\Exception $e) {
                return null;
            }

            // If we haven't balance to pay new workItem
            if ($contractStatus->workItemsBalance < 1) {
                $this->status = Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS;
                $this->save();
                return null;
            }

            /** @var WorkItem $newWorkItem */
            $newWorkItem = WorkItem::find()
                ->where(['task_id' => $this->id])
                ->andWhere(['status' => WorkItem::STATUS_FREE])
                ->one();

            if ($newWorkItem === null || !$newWorkItem->lock()) {
                return null;
            }

            $newWorkItem->moderator_id = $moderator->id;
            $newWorkItem->moderator_address = $moderator->eth_addr;
            $newWorkItem->status = WorkItem::STATUS_IN_HAND;

            if ($newWorkItem->save()) {
                $newWorkItem->unlock();
                $currentWorkItem = $newWorkItem;
            }
        }

        $dataLabel = $currentWorkItem->getNewDataLabel();

        if ($dataLabel === null && $currentWorkItem->status === WorkItem::STATUS_IN_HAND) {
            $currentWorkItem->status = WorkItem::STATUS_READY;
            $currentWorkItem->save();
        }

        return $dataLabel;
    }



    public function isContractNew()
    {
        return $this->status == self::STATUS_CONTRACT_NEW;
    }

    public function isContractActive()
    {
        return $this->status == self::STATUS_CONTRACT_ACTIVE;
    }

    public function setContractActive($save = true)
    {
        $this->status = self::STATUS_CONTRACT_ACTIVE;
        $save && $this->save(false, ['status']);
        return $this;
    }

    public function isContractDeploying()
    {
        return $this->status == self::STATUS_CONTRACT_DEPLOYMENT_PROCESS;
    }

    public function setContractDeploymentError($save = true)
    {
        $this->status = self::STATUS_CONTRACT_DEPLOYMENT_ERROR;
        $save && $this->save(false, ['status']);
        return $this;
    }

    public function isPaused()
    {
        return $this->status == self::STATUS_CONTRACT_ACTIVE_PAUSED;
    }

    public function isCompleted()
    {
        return $this->status == self::STATUS_CONTRACT_ACTIVE_COMPLETED;
    }

    public function isFinalized()
    {
        return $this->status == self::STATUS_CONTRACT_FINALIZED;
    }

    public function setFinalized($save = true)
    {
        $this->status = self::STATUS_CONTRACT_FINALIZED;
        $save && $this->save(false, ['status']);
        return $this;
    }

    public function isForceFinalizing()
    {
        return $this->status == self::STATUS_CONTRACT_FORCE_FINALIZING;
    }

    public function setForceFinalizing($save = true)
    {
        $this->status = self::STATUS_CONTRACT_FORCE_FINALIZING;
        $save && $this->save(false, ['status']);
        return $this;
    }


    /**
     * Runs console command blockchain/update-completed-work for this task.
     */
    public function updateCompletedWork() : bool
    {
        // Task status must be ACTIVE
        if ($this->status !== Task::STATUS_CONTRACT_ACTIVE) return false;

        try {
            $c = new \console\controllers\BlockchainController(Yii::$app->controller->id, Yii::$app);
            $c->runAction('update-completed-work', ['taskId' => $this->id]);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


    /**
     * @param $contractStatus
     */
    public function syncScoringWithBlockchain($contractStatus)
    {
        $workItemsInBlockchain = json_decode(json_encode($contractStatus->workers), true);
        $workItemsInDb = [];

        $approvedWorks = (new \yii\db\Query)
            ->select(['moderator_address', 'COUNT(moderator_address) AS count'])
            ->from(WorkItem::tableName())
            ->where(['task_id'=>$this->id])
            ->andWhere(['status'=>WorkItem::STATUS_APPROVED])
            ->groupBy(['moderator_address'])
            ->all();

        foreach ($approvedWorks as $work) {
            $workItemsInDb[$work['moderator_address']]['approvedItems'] = (int) $work['count'];
        }

        $declinedWorks = (new \yii\db\Query)
            ->select(['moderator_address', 'COUNT(moderator_address) AS count'])
            ->from(WorkItem::tableName())
            ->where(['task_id'=>$this->id])
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
            $moderator_address = new Address($address);
            $this->approveWorkItems($moderator_address, $num);
        }

        foreach ($declinedWorksToUpdate as $address => $num) {
            $moderator_address = new Address($address);
            $this->declineWorkItems($moderator_address, $num);
        }
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


    /**
     * @param Address $moderator_address
     * @return int
     */
    public function readyWorkItemsNumber(Address $moderator_address) : int
    {
        $readyCount = WorkItem::find()
            ->where(['task_id' => $this->id])
            ->andWhere(['moderator_address'=>$moderator_address])
            ->andWhere(['status' => WorkItem::STATUS_READY])
            ->count();

        return (int) $readyCount;
    }

    /**
     * @param Address $moderator_address
     * @param int $num
     * @return WorkItem[]
     */
    public function readyWorkItems(Address $moderator_address, int $num)
    {
        $readWorkItems= WorkItem::find()
            ->where(['task_id' => $this->id])
            ->andWhere(['moderator_address' => $moderator_address])
            ->andWhere(['status' => WorkItem::STATUS_READY])
            ->limit($num)
            ->orderBy('updated_at')
            ->all();

        return $readWorkItems;
    }

    /**
     * @param Address $moderator_address
     * @param int $num
     * @return bool
     */
    public function approveWorkItems(Address $moderator_address, int $num=1) : bool
    {
        $readyWorkItemsNumber = $this->readyWorkItemsNumber($moderator_address);
        if ($readyWorkItemsNumber < $num) return false;

        $readyWorkItems = $this->readyWorkItems($moderator_address, $num=1);

        foreach ($readyWorkItems as $readyWorkItem) {
            $readyWorkItem->approve();
        }

        return true;
    }

    public function declineWorkItems(Address $address, int $num=1) : bool
    {
        $readyWorkItemsNumber = $this->readyWorkItemsNumber($address);
        if ($readyWorkItemsNumber < $num) return false;

        $readyWorkItems = $this->readyWorkItems($address, $num=1);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($readyWorkItems as $readyWorkItem) {
                $readyWorkItem->decline();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            try {
                $transaction->rollBack();
            } catch (\Exception $e) {
                return false;
            }
            return false;
        }

        return true;
    }


    /**
     * @return WorkItem|null
     */
    public function getRandomFreeWorkItem() :?WorkItem
    {
        $randomFreeWorkItem = null;

        $freeWorkItems = $this->getWorkItems()
            ->where(['status'=>WorkItem::STATUS_FREE])
            ->all();

        while (true) {
            if ($freeWorkItems === []) return null;

            $key = array_rand($freeWorkItems);
            /** @var WorkItem $randomFreeWorkItem */
            $randomFreeWorkItem = $freeWorkItems[$key];

            if (!$randomFreeWorkItem->lock()) {
                unset($freeWorkItems[$key]);
                continue;
            }
            break;
        }

        return $randomFreeWorkItem;
    }


    /**
     * @return bool|string
     */
    public function createCsvFile()
    {
        try {
            $approvedWorkItems = $this->getWorkItems()
                ->andWhere(['status' => WorkItem::STATUS_APPROVED])
                ->all();

            /** @var \yii2tech\filestorage\local\Storage $fileStorage */
            $bucket = $this->getResultFileBucket();
            $resource = $bucket->openFile($this->createTaskResultFileName(), 'w');

            foreach ($approvedWorkItems as $workItem) {
                /** @var DataLabel $dataLabel */
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

            $this->result_file = $this->createTaskResultFileName();
            $this->save(false, ['result_file']);

            return $this->result_file;
        } catch (\Exception $e) {
            Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
        return false;
    }

    /**
     * @return \yii2tech\filestorage\local\Bucket
     */
    public function getResultFileBucket() : \yii2tech\filestorage\local\Bucket
    {
        /** @var \yii2tech\filestorage\local\Storage $fileStorage */
        $fileStorage = Yii::$app->fileStorage;
        return $fileStorage->getBucket('result');
    }

    /**
     * @return string
     */
    private function createTaskResultFileName() : string
    {
        return sprintf('%s_task_result.csv', $this->id);
    }

    /**
     * @return array
     */
    public function getModeratorCountDataLabels(): array
    {
        /** @var WorkItem[] $inHands */
        $inHands = $this
            ->getWorkItems()
            ->where(['IN', 'status', [WorkItem::STATUS_IN_HAND, WorkItem::STATUS_READY]])
            ->all();

        $counts = [];
        foreach ($inHands as $workItem) {
            $address = $workItem->moderator_address;
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

}
