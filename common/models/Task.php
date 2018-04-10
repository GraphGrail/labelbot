<?php

namespace common\models;

use common\components\EthereumGateway;
use common\domain\ethereum\Address;
use common\domain\ethereum\Contract;
use common\models\BlockchainCallback;
use common\models\AssignedLabel;
use common\models\Data;
use common\interfaces\BlockchainGatewayInterface;
use Yii;

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
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => null,
            ],
            'typecast' => [
                'class' => \yii\behaviors\AttributeTypecastBehavior::className(),
                'typecastAfterFind' => true,
            ],
            'deletedAttribute' => [
                'class' => \common\models\behavior\DeletedAttributeBehavior::className(),
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
        return $this->hasOne(Dataset::className(), ['id' => 'dataset_id'])->one();
    }

    public function getLabelGroup()
    {
        return $this->hasOne(LabelGroup::className(), ['id' => 'label_group_id'])->one();
    }

    public function getAssignedLabels()
    {
        return $this->hasMany(AssignedLabel::className(), ['task_id' => 'id']);
    }

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
            throw new \Exception("Can't save Callback after deployContract() was called");
        }

        $this->contract = json_encode($contract);

        $this->status = Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS;
        if (!$this->save()) {
            throw new \Exception("Can't update Task");
        }
    }

    public function contractAddress() : ?Address
    {
        return $this->contract_address ? new Address($this->contract_address) : null;
    }
    

    public function getDataForLabelAssignment(int $moderator_id) : ?AssignedLabel
    {
        AssignedLabel::handleSkippedLabels();

        // Are moderator start new workItem or not?
        if ($this->isGettingNewWorkItem($moderator_id)) {
            $blockchain      = new EthereumGateway;
            $contractAddress = new Address($this->contract_address);
            $contractStatus  = $blockchain->contractStatus($contractAddress);

            // If there is no workItems, all of them are already in work
            if ($contractStatus->workItemsLeft <= $this->currentWorkItemsCount()) {
                return null;
            }
            // If we haven't balance to pay new workItem 
            if ($contractStatus->workItemsBalance < 1) {
                $this->status = Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS;
                $this->save();
                return null;
            }
        }
        // if moderator continue work on workItem
        else {
            if ($this->work_item_size <= $this->workItemAssignsCount($moderator_id)) {
                return null;
            }
        }

        $assigned_label = null;
        $endTime = time() + 60;
        while(true) {
            /** @var AssignedLabel $assigned_label */
            $assigned_label = AssignedLabel::find()
                ->where(['task_id'=>$this->id])
                ->andWhere(['in', 'status', [AssignedLabel::STATUS_NEW, AssignedLabel::STATUS_SKIPPED]])
                ->andWhere(['or', 'moderator_id is null', 'moderator_id' => $moderator_id])
                ->orderBy('updated_at')
                ->one();

            if ($assigned_label === null) return null;

            if (Lock::create($assigned_label)) {
                break;
            }
            if (time() > $endTime) {
                return null;
            }
            sleep(1);
        }

        // We update AssignedLabel with STATUS_IN_HAND to prevent other moderators to get same data at one moment.
        $assigned_label->moderator_id = $moderator_id;
        $assigned_label->status = AssignedLabel::STATUS_IN_HAND;
        $assigned_label->save();

        Lock::free($assigned_label);

        return $assigned_label;
    }

    private function isGettingNewWorkItem(int $moderator_id) : bool
    {
        // If there is not approved or declined AssignedLabel for moderator
        $assignedLabel = AssignedLabel::find()
            ->where(['task_id' => $this->id])
            ->andWhere(['moderator_id' => $moderator_id])
            ->andWhere(['OR', ['status' => AssignedLabel::STATUS_IN_HAND], ['status' => AssignedLabel::STATUS_READY]])
            ->one();

        return $assignedLabel === null ? true : false;
    }

    private function workItemAssignsCount(int $moderator_id) : int
    {
        $count = AssignedLabel::find()
            ->select('count')
            ->where(['task_id' => $this->id])
            ->andWhere(['moderator_id' => $moderator_id])
            ->andWhere(['OR', ['status' => AssignedLabel::STATUS_IN_HAND], ['status' => AssignedLabel::STATUS_READY]])
            ->count();

        return $count;
    }

    private function currentWorkItemsCount(): int
    {
        $count = AssignedLabel::find()
            ->select('moderator_id')
            ->where(['task_id' => $this->id])
            ->andWhere(['in', 'status', [AssignedLabel::STATUS_IN_HAND, AssignedLabel::STATUS_READY]])
            ->groupBy('moderator_id')
            ->count();

        return $count;
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

    public function readyWorkItemsNumber(Moderator $moderator) : int
    {
        $readyCount = AssignedLabel::find()
            ->where(['task_id' => $this->id])
            ->andWhere(['moderator_id'=>$moderator->id])
            ->andWhere(['status' => AssignedLabel::STATUS_READY])
            ->count();

        $readyWorkItems = (int) ($readyCount/$this->work_item_size);
        return $readyWorkItems;
    }

    public function approveWorkItems(Moderator $moderator, int $num=1) : bool
    {
        $readyWorkItems = $this->readyWorkItemsNumber($moderator);
        if ($readyWorkItems < $num) return false;

        $updates = AssignedLabel::updateStatuses(
            $this->id,
            AssignedLabel::STATUS_READY,
            AssignedLabel::STATUS_APPROVED,
            $moderator->id,
            $this->work_item_size * $num
        );

        return $updates ? true : false;
    }

    public function declineWorkItems(Moderator $moderator, int $num=1) : bool
    {
        $readyWorkItems = $this->readyWorkItemsNumber($moderator);
        if ($readyWorkItems < $num) return false;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            AssignedLabel::updateStatuses(
                $this->id,
                AssignedLabel::STATUS_READY,
                AssignedLabel::STATUS_DECLINED,
                $moderator->id,
                $this->work_item_size * $num
            );
            AssignedLabel::copyDeclinedToNew($this->id, $moderator->id, $this->work_item_size * $num);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }

        return true;
    }

}
