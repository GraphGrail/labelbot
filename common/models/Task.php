<?php

namespace common\models;

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
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * Statuses
     */
    const STATUS_CONTRACT_NOT_DEPLOYED       = 1;
    const STATUS_CONTRACT_DEPLOYMENT_PROCESS = 2;
    const STATUS_CONTRACT_DEPLOYMENT_ERROR   = 3;
    const STATUS_CONTRACT_NEW       = 4;
    const STATUS_CONTRACT_ACTIVE    = 5;
    const STATUS_CONTRACT_FINALIZED = 6;

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
            [['id', 'user_id', 'dataset_id', 'label_group_id'], 'integer'],
            [['description', 'contract'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['contract_address'], 'string', 'max' => 42],
            [['status'], 'integer'],
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
}
