<?php

namespace common\models;

use common\models\behavior\DeletedAttributeBehavior;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "label_group".
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
class LabelGroup extends ActiveRecord
{
    const STATUS_OK = 1;
    const STATUS_NO_LABELS_TREE = 2;
    const STATUS_LABELS_TREE_ERROR = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'label_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 300],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string', 'max' => 6000],
            [['labels_tree'], 'string', 'max' => 60000],
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
            'user_id'     => 'User ID',
            'name'        => 'Name',
            'description' => 'Description',
            'labels_tree' => 'Labels Tree',
            'status'      => 'Status',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
        ];
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
    public function getLabels()
    {
        return $this->hasMany(Label::class, ['label_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['label_group_id' => 'id']);
    }

    /**
     * @return LabelGroupQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new LabelGroupQuery(get_called_class());
    }

}
