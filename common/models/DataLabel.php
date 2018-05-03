<?php

namespace common\models;



/**
 * This is the model class for table "data_label".
 *
 * @property int $id
 * @property int $work_item_id
 * @property int $data_id
 * @property int $label_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class DataLabel extends \yii\db\ActiveRecord
{
    /**
     * Statuses
     */
    const STATUS_NEW      = 10;
    const STATUS_READY    = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data_label';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['work_item_id', 'data_id'], 'required'],
            [['work_item_id', 'data_id', 'label_id'], 'integer'],
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
            'data_id' => 'Data ID',
            'work_item_id' => 'Work item ID',
            'label_id' => 'Label ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    /**
     * Returns related Task model
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Returns related WorkItem model
     * @return \yii\db\ActiveQuery
     */
    public function getWorkItem()
    {
        return $this->hasOne(WorkItem::class, ['id' => 'work_item_id']);
    }

    /**
     * Returns related Data model
     * @return \yii\db\ActiveQuery
     */
    public function getData()
    {
        return $this->hasOne(Data::class, ['id' => 'data_id']);
    }

    /**
     * Returns related Label model
     * @return \yii\db\ActiveQuery
     */
    public function getLabel()
    {
        return $this->hasOne(Label::class, ['id' => 'label_id']);
    }

    /**
     * Returns related Moderator model
     * @return \yii\db\ActiveQuery
     */
    public function getModerator()
    {
        return $this->hasOne(Moderator::class, ['id' => 'moderator_id']);
    }


    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function skip() : bool
    {
        if ($this->getWorkItem()->one()->status !== WorkItem::STATUS_IN_HAND) return false;

        /** @var WorkItem $freeWorkItem */
        $freeWorkItem = $this
            ->getWorkItem()->one()
            ->getTask()->one()
            ->getRandomFreeWorkItem();

        if ($freeWorkItem === null) return false;

        /** @var DataLabel $randomDataLabel */
        $randomDataLabel = $freeWorkItem->getRandomDatalabel();

        $id = $randomDataLabel->work_item_id;
        $randomDataLabel->work_item_id = $this->work_item_id;
        $this->work_item_id = $id;

        $transaction = static::getDb()->beginTransaction();
        try {
            $this->save();
            $randomDataLabel->save();
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            $freeWorkItem->unlock();
            return false;
        }

        $freeWorkItem->unlock();
        return true;
    }


}
