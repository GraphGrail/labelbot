<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "blockchain_callback".
 *
 * @property int $id
 * @property int $type
 * @property string $params
 * @property string $callback_id
 * @property int $received
 * @property int $success
 * @property string $error
 * @property string $payload
 * @property int $created_at
 * @property int $updated_at
 */
class BlockchainCallback extends \yii\db\ActiveRecord
{
    /**
     * Callback Types
     */
    const DEPLOY_CONTRACT       = 1;
    const UPDATE_COMPLETED_WORK = 2;
    const FORCE_FINALIZE        = 3;
    const CREDIT_ACCOUNT        = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blockchain_callback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['callback_id'], 'string', 'max' => 32],
            [['callback_id'], 'required'],
            [['callback_id'], 'unique'],
            [['type'], 'integer'],
            [['error', 'payload'], 'string'],
            [['received', 'success'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'callback_id' => Yii::t('app', 'Callback ID'),
            'received' => Yii::t('app', 'Received'),
            'success' => Yii::t('app', 'Success'),
            'error' => Yii::t('app', 'Error'),
            'payload' => Yii::t('app', 'Payload'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

}
