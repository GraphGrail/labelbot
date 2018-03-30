<?php

namespace common\models;

use common\domain\ethereum\Address;
use common\domain\ethereum\Contract;
use common\models\BlockchainCallback;
use common\interfaces\BlockchainGatewayInterface;
use common\models\behavior\DeletedAttributeBehavior;
use Yii;
use yii\behaviors\AttributeTypecastBehavior;

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
 */
class Task extends ActiveRecord
{
    /**
     * Statuses
     */
    const STATUS_CONTRACT_NOT_DEPLOYED       = 1;
    const STATUS_CONTRACT_DEPLOYMENT_PROCESS = 2;
    const STATUS_CONTRACT_DEPLOYMENT_ERROR   = 3;
    const STATUS_CONTRACT_NEW_NEED_TOKENS    = 4;
    const STATUS_CONTRACT_NEW                = 5;
    const STATUS_CONTRACT_ACTIVE             = 6;
    const STATUS_CONTRACT_ACTIVE_PAUSED      = 7;
    const STATUS_CONTRACT_ACTIVE_NEED_TOKENS = 8;
    const STATUS_CONTRACT_FORCE_FINALIZING   = 9;
    const STATUS_CONTRACT_FINALIZED          = 10;

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
            [['dataset_id', 'label_group_id', 'name', 'status'], 'required'],
        //    [['id', 'user_id', 'dataset_id', 'label_group_id', 'name', 'description', 'contract_address', 'contract', 'status'], 'required'],
            [['id', 'dataset_id', 'label_group_id'], 'integer'],
            [['description', 'contract'], 'string'],
            [['name', 'delivering_job_id'], 'string', 'max' => 255],
            [['contract_address'], 'string', 'max' => 42],
            [['status'], 'integer'],
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
                'class' => AttributeTypecastBehavior::className(),
                'typecastAfterFind' => true,
            ],
            'deletedAttribute' => [
                'class' => DeletedAttributeBehavior::className(),
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


    public function deployContract(BlockchainGatewayInterface $blockchain, Address $clientAddress)
    {
        $contract    = new Contract($clientAddress, 20); // TODO: real jobs number
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

    public function tokensNeededForContractActivation()
    {
        if ($this->contract === null) {
            return null;
        }

        $contract = json_decode($this->contract);
        return $contract->totalWorkItems * $contract->workItemPrice;
    }

    public function contractAddress() : Address
    {
        return $this->contract_address ? new Address($this->contract_address) : null;
    }

}
