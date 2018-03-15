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
                'skipOnEmpty' => false,
                'checkExtensionByMimeType' => false, // Without that validation by extension 'csv' don't work.
                'extensions' => 'csv',
                'maxSize' => Yii::$app->params['datasetFileMaxSize'],
                'maxFiles' => 1,
            ],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {

            $dataset = new Dataset;
            $dataset->user_id = Yii::$app->user->id;
            $dataset->name = $this->name;
            $dataset->description = $this->description;

            if (!$dataset->updateStatus(Dataset::STATUS_UPLOADING)) {
                // TODO: add error to $model
                return false;
            }

            $datasetsDir = Yii::getAlias(Yii::$app->params['datasetsUploadDir']);

            if (!file_exists($datasetsDir)) {
                mkdir($datasetsDir, 0777, true);
            }

            $filePath = $datasetsDir . $dataset->id .'-'. $dataset->user_id .'.'. $this->datasetFile->extension;
            
            if (!$this->datasetFile->saveAs($filePath)) {
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
        } else {
            return false;
        }
    }
}