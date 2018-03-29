<?php

namespace common\models;

use common\models\behavior\DeletedAttributeBehavior;
use \common\models\Label;
use Yii;
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
class LabelGroup extends \yii\db\ActiveRecord
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
            'user_id'     => 'User ID',
            'name'        => 'Name',
            'description' => 'Description',
            'labels_tree' => 'Labels Tree',
            'status'      => 'Status',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getLabels()
    {
        return $this->hasMany(Label::className(), ['label_group_id' => 'id']);
    }

    public function getDatasets()
    {
        return $this->hasMany(Dataset::className(), ['id' => 'dataset_id'])
            ->viaTable('label_group_to_dataset', ['label_group_id' => 'id']);    
    }

}
