<?php

namespace frontend\models;

use common\models\Dataset;
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
                'maxSize' => 40*1024*1024, // 40mb, 2000 strings
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

            if (!$dataset->updateStatus(Dataset::UPLOADING)) {
                // TODO: add error to $model
                return false;
            }

            $filePath = Yii::getAlias("@runtime/uploads/datasets/") . $dataset->id .'-'. $dataset->user_id .'.'. $this->datasetFile->extension;
            
            if (!$this->datasetFile->saveAs($filePath)) {
                $dataset->updateStatus(Dataset::UPLOADING_ERROR);
                return false;
            }

            $dataset->updateStatus(Dataset::UPLOADED);

            // TODO: start dataset parsing

            return true;
        } else {
            return false;
        }
    }
}