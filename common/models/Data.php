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
            [['dataset_id', 'data_raw'], 'required'],
            [['dataset_id'], 'integer'],
            [['data', 'data_raw'], 'string'],
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
        return $this->hasOne(Dataset::class, ['id' => 'dataset_id']);
    }

    /**
     * Returns DataLabels for this Data
     * 
     * @return  yii\db\ActiveQueryInterface
     */
    public function getDataLabels()
    {
        return $this->hasMany(DataLabel::class, ['data_id' => 'id']);
    }

}
