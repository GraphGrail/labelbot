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

    public static function getForLabelAssignment($dataset_id, $moderator_id)
    {
        // At first tryin' to find unlabeled data.
        // Then tryin' to least labeled data that moderator don't assign label.
        $data = self::getUnlabeledData($dataset_id) 
             ?: self::getLeastLabeledData($dataset_id, $moderator_id);

        if ($data === null) return null;

        // We create AssignedLabel instance with label_id=NULL 
        // to prevent moderators getting same data at one moment.
        $assigned_label = new AssignedLabel;
        $assigned_label->data_id = $data->id;
        $assigned_label->moderator_id = $moderator_id;
        $assigned_label->save();
        // TODO: If moderators don't assign label at some time after they 
        // get data, we can delete this AssignedLabel records.

        return $data;
    }

    /*private static function getUnassignedLabel(int $dataset_id, int $moderator_id)
    {
        $unassigned_label = AssignedLabel::findOne([
            'moderator_id' => $moderator_id,
            'label_id' => null
        ]);
        return self::findOne($unassigned_label->data_id);
    }*/

    private static function getUnlabeledData(int $dataset_id)
    {
        $unlabeled_data_id = Yii::$app->db->createCommand("
                SELECT `data`.id FROM `data` 
                LEFT JOIN `assigned_label` on `data`.id = `assigned_label`.data_id 
                WHERE `data`.dataset_id = $dataset_id AND `assigned_label`.id IS NULL
            ")->queryOne();

        return self::findOne($unlabeled_data_id);
    }

    private static function getLeastLabeledData(int $dataset_id, int $moderator_id)
    {
        $data = Yii::$app->db->createCommand("
                SELECT data_id, COUNT(data_id) as assigns_count 
                FROM `assigned_label` 
                JOIN `data` on `data`.id = `assigned_label`.data_id
                WHERE `data`.dataset_id = $dataset_id AND data_id NOT IN (
                    SELECT data_id FROM `assigned_label`
                    JOIN `data` on `data`.id = `assigned_label`.data_id
                    WHERE `data`.dataset_id = $dataset_id AND `assigned_label`.moderator_id = $moderator_id
                )
                GROUP BY data_id
                ORDER BY assigns_count ASC
            ")->queryOne();

        return self::findOne($data['data_id']);
    }
}
