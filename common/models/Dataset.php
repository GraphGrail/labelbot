<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "dataset".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $description
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Dataset extends \yii\db\ActiveRecord
{
    /**
     * Dataset statuses
     */
    const STATUS_READY           = 1;
    const STATUS_UPLOADING       = 2;
    const STATUS_UPLOADING_ERROR = 3;
    const STATUS_UPLOADED        = 4;
    const STATUS_PARSING         = 5;
    const STATUS_PARSING_ERROR   = 6;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dataset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 200],
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
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getData()
    {
        return $this->hasMany(Data::className(), ['dataset_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getLabelGroups()
    {
        return $this->hasMany(LabelGroup::className(), ['id' => 'label_group_id'])
            ->viaTable('label_group_to_dataset', ['dataset_id' => 'id']);    
    }

    public function updateStatus(int $status) : bool
    {
        $this->status = $status;
        return $this->save();
    }
}
