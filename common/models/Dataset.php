<?php

namespace common\models;

use common\models\behavior\DeletedAttributeBehavior;
use Yii;
use yii\behaviors\AttributeTypecastBehavior;

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
 * @property bool $deleted
 */
class Dataset extends ActiveRecord
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

    public function getDataCount()
    {
        return $this->hasMany(Data::className(), ['dataset_id' => 'id'])->count();
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['dataset_id' => 'id']);
    }

/*    public function getLabelGroups()
    {
        return $this->hasMany(LabelGroup::className(), ['id' => 'label_group_id'])
            ->viaTable('label_group_to_dataset', ['dataset_id' => 'id']);    
    }*/

    public function updateStatus(int $status) : bool
    {
        $this->status = $status;
        return $this->save();
    }

    public function status(): object {
        $status = [];
        switch ($this->status) {
            case self::STATUS_READY:
                $status = [
                    'text' => Yii::t('app', 'Ready'),
                    'tip' => '',
                    'color' => 'success'
                ];
                break;
            case self::STATUS_UPLOADING:
                $status = [
                    'text' => Yii::t('app', 'Uploading'),
                    'tip' => '',
                    'color' => 'info'
                ];
                break;
            case self::STATUS_UPLOADING_ERROR:
                $status = [
                    'text' => Yii::t('app', 'Uploading error'),
                    'tip' => '',
                    'color' => 'danger'
                ];
                break;
            case self::STATUS_UPLOADED || self::STATUS_PARSING:
                $status = [
                    'text' => Yii::t('app', 'Processing'),
                    'tip' => '',
                    'color' => 'info'
                ];
                break;
            case self::STATUS_PARSING_ERROR:
                $status = [
                    'text' => Yii::t('app', 'Processing error'),
                    'tip' => '',
                    'color' => 'danger'
                ];
                break;
        }
        return (object) $status;
    }

    public static function find()
    {
        return new DatasetQuery(get_called_class());
    }
}
