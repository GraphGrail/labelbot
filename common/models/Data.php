<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "data".
 *
 * @property int $id
 * @property int $dataset_id
 * @property string $data
 * @property string $data_raw
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
            [['data', 'data_raw'], 'string'],
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

    /**
     * Returns Dataset for this Data
     * 
     * @return  yii\db\ActiveQueryInterface
     */
    public function getDataset()
    {
        return $this->hasOne(Dataset::className(), ['id' => 'dataset_id']);
    }

    /**
     * Returns AssignedLabels for this Data
     * 
     * @return  yii\db\ActiveQueryInterface
     */
    public function getAssignedLabels()
    {
        return $this->hasMany(AssignedLabel::className(), ['label_id' => 'id']);
    }

    /**
     * Gets Data from specified dataset for label Assignment by specified moderator
     * 
     * @param type $dataset_id 
     * @param type $moderator_id 
     * @return Data
     */
    public static function getForLabelAssignment($dataset_id, $moderator_id)
    {
        self::deleteUnassignedLabels();

        $data = self::getUnlabeledData($dataset_id) 
             ?: self::getSkippedData($dataset_id, $moderator_id)
             ?: self::getLeastLabeledData($dataset_id, $moderator_id);

        if ($data === null) return null;

        // We create AssignedLabel instance with label_id=NULL 
        // to prevent moderators getting same data at one moment.
        $assigned_label = new AssignedLabel;
        $assigned_label->data_id = $data->id;
        $assigned_label->moderator_id = $moderator_id;
        $assigned_label->save();

        return $data;
    }

    /**
     * Deletes AssignedLabels that weren't assigned in 5 min
     * 
     * @return int
     */
    private static function deleteUnassignedLabels()
    {
        $expired_time = time() - 5 * 60;
        return AssignedLabel::deleteAll(
            'label_id IS NULL AND created_at < :expired_at',
            [':expired_at' => $expired_time]
        );
    }

    /**
     * Returns first unlabeled Data from data from specified dataset
     * 
     * @param int $dataset_id 
     * @return Data
     */
    private static function getUnlabeledData(int $dataset_id)
    {
        $unlabeled_data_id = Yii::$app->db->createCommand("
                SELECT `data`.id 
                FROM `data` 
                LEFT JOIN `assigned_label` on `data`.id = `assigned_label`.data_id 
                WHERE `data`.dataset_id = $dataset_id 
                    AND `assigned_label`.id IS NULL
                LIMIT 1
            ")->query();

        return self::findOne($unlabeled_data_id);
    }

    /**
     * Returns first Data that was skipped by any moderator except specified
     * 
     * @param int $dataset_id 
     * @param int $moderator_id 
     * @return Data
     */
    private static function getSkippedData(int $dataset_id,  int $moderator_id)
    {
        $skipped_data_id = Yii::$app->db->createCommand("
                SELECT data_id 
                FROM `assigned_label`
                JOIN `data` on `data`.id = `assigned_label`.data_id
                WHERE `data`.dataset_id = $dataset_id 
                    AND `assigned_label`.label_id = 0
                    AND data_id NOT IN (
                        SELECT data_id FROM `assigned_label`
                        JOIN `data` on `data`.id = `assigned_label`.data_id
                        WHERE `data`.dataset_id = $dataset_id 
                            AND `assigned_label`.label_id = 0
                            AND `assigned_label`.moderator_id = $moderator_id
                    )
                LIMIT 1
            ")->query();

        return self::findOne($skipped_data_id);
    }

    /**
     * Returns first least labeled Data that was labeled by any moderator except specified
     * 
     * @param int $dataset_id 
     * @param int $moderator_id 
     * @return Data
     */
    private static function getLeastLabeledData(int $dataset_id, int $moderator_id)
    {
        $data = Yii::$app->db->createCommand("
                SELECT data_id, COUNT(data_id) as assigns_count 
                FROM `assigned_label` 
                JOIN `data` on `data`.id = `assigned_label`.data_id
                WHERE `data`.dataset_id = $dataset_id 
                    AND data_id NOT IN (
                        SELECT data_id FROM `assigned_label`
                        JOIN `data` on `data`.id = `assigned_label`.data_id
                        WHERE `data`.dataset_id = $dataset_id 
                            AND `assigned_label`.moderator_id = $moderator_id
                    )
                GROUP BY data_id
                ORDER BY assigns_count ASC
                LIMIT 1
            ")->queryOne();

        return self::findOne($data['data_id']);
    }
}
