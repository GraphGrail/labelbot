<?php

namespace common\models;


/**
 * This is the model class for table "moderator".
 *
 * @property int $id
 * @property string $auth_token
 * @property string $eth_addr
 * @property int $tg_chat_id
 * @property int $tg_id
 * @property string $tg_username
 * @property string $tg_first_name
 * @property string $tg_last_name
 * @property string $phone
 * @property string $current_task
 * @property int $created_at
 * @property int $updated_at
 */
class Moderator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'moderator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tg_chat_id', 'tg_id'], 'integer'],
            [['tg_id'], 'required'],
            [['eth_addr'], 'string', 'max' => 42],
            [['auth_token'], 'string', 'max' => 64],
            [['tg_username', 'tg_first_name', 'tg_last_name'], 'string', 'max' => 200],
            [['phone'], 'string', 'max' => 20],
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
            'id' => 'ID',
            'auth_token' => 'Auth Token',
            'eth_addr' => 'Ethereum address',
            'tg_chat_id' => 'Tg Chat ID',
            'tg_id' => 'Tg ID',
            'tg_username' => 'Tg Username',
            'tg_first_name' => 'Tg First Name',
            'tg_last_name' => 'Tg Last Name',
            'phone' => 'Phone',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
