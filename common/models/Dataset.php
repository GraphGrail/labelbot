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
            \yii\behaviors\TimestampBehavior::class,
            [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => null,
            ],
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'typecastAfterFind' => true,
            ],
            'deletedAttribute' => [
                'class' => DeletedAttributeBehavior::class,
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getData()
    {
        return $this->hasMany(Data::class, ['dataset_id' => 'id']);
    }

    /**
     * @return int|string
     */
    public function getDataCount()
    {
        return $this->hasMany(Data::class, ['dataset_id' => 'id'])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['dataset_id' => 'id']);
    }

    /**
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $status) : bool
    {
        $this->status = $status;
        return $this->save();
    }

    /**
     * @return \stdClass
     */
    public function status() : \stdClass
    {
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
            case self::STATUS_UPLOADED:
            case self::STATUS_PARSING:
                $status = [
                    'text' => Yii::t('app', 'Processing'),
                    'tip' => '',
                    'color' => 'info',
                    'reload' => true
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
