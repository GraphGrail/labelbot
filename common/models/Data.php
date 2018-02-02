<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "data".
 *
 * @property int $id
 * @property int $dataset_id
 * @property string $data
 */
class Data extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'dataset_id'], 'required'],
            [['id', 'dataset_id'], 'integer'],
            [['data'], 'string'],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dataset_id' => 'Dataset ID',
            'data' => 'Data',
        ];
    }

    public function getDataset()
    {
        return $this->hasOne(Dataset::className(), ['id' => 'dataset_id']);
    }

    public function getAssignedLabels()
    {
        return $this->hasMany(AssignedLabel::className(), ['label_id' => 'id']);
    }

    public function assignLabel(Label $label, $moderator_id) : bool
    {
        $assignedLabel = new AssignedLabel();
        $assignedLabel->data_id = $this->id;
        $assignedLabel->label_id = $label->id;
        $assignedLabel->moderator_id = $moderator_id;
        return $assignedLabel->save();
    }

    public static function getForLabelAssignment()
    {
        // TODO: Business logic for getData
        // At first tryin' to find unlabeled data
        $unlabeled_data_id = Yii::$app->db->createCommand("
                SELECT `data`.id FROM `data` 
                LEFT JOIN `assigned_label` on `data`.id = `assigned_label`.data_id 
                WHERE `assigned_label`.id IS NULL
            ")->queryOne();

        if (!$unlabeled_data_id) {
            // TODO:
            // then tryin' to less labeled data that moderator don't assign label            
        }

        return Data::findOne($unlabeled_data_id);
    }
}
