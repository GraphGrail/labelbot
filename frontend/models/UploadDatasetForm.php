<?php

namespace frontend\models;

use common\models\Dataset;
use console\jobs\ParseDatasetJob;
use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

class UploadDatasetForm extends Model
{
    public $name;

    public $description;

    /**
     * @var UploadedFile
     */
    public $datasetFile;

    public function rules()
    {
        return [
            [['name', 'datasetFile'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['description'], 'string', 'max' => 6000],
            [['datasetFile'], 'file', 
                'checkExtensionByMimeType' => false, // Without that validation by extension 'csv' don't work.
                'extensions'  => 'csv',
                'skipOnEmpty' => false,
                'maxSize'     => Yii::$app->params['datasetFileMaxSize'],
                'maxFiles'    => 1,
            ],
        ];
    }
    
    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }

        $dataset = new Dataset;
        $dataset->name        = $this->name;
        $dataset->description = $this->description;

        if (!$dataset->updateStatus(Dataset::STATUS_UPLOADING)) {
            // TODO: add error to $model
            return false;
        }
        $filePath = $dataset->id .'-'. $dataset->user_id .'.'. $this->datasetFile->extension;

        /** @var \yii2tech\filestorage\local\Storage $fileStorage */
        $fileStorage = Yii::$app->fileStorage;
        $bucket = $fileStorage->getBucket('datasets');

        if(!$bucket->moveFileIn($this->datasetFile->tempName, $filePath)) {
            $dataset->updateStatus(Dataset::STATUS_UPLOADING_ERROR);
            return false;
        }

        $dataset->updateStatus(Dataset::STATUS_UPLOADED);

        // TODO: Here we need to check that csv file has valid format!!

        Yii::$app->queue->push(new ParseDatasetJob([
            'dataset_id' => $dataset->id,
            'file'       => $filePath,
        ]));

        return true;
    }
}