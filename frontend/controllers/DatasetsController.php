<?php

namespace frontend\controllers;

use frontend\models\UploadDatasetForm;
use yii\filters\AccessControl;
use yii\helpers\StringHelper;
use yii\web\Controller;
use yii\web\UploadedFile;
use Yii;

class DatasetsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Show Datasets list
     * @return type
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * New Dataset Uploading
     * @return type
     */
    public function actionNew()
    {
        $model = new UploadDatasetForm();

        if (Yii::$app->request->isPost) {
            $modelName = StringHelper::basename(get_class($model));
            $model->setAttributes(Yii::$app->request->post($modelName));
            $model->datasetFile = UploadedFile::getInstance($model, 'datasetFile');

            if ($model->upload()) {
                // file is uploaded successfully
                $this->redirect('index');
            }
        }

        return $this->render('new', ['model' => $model]);
    }


    public function actionDelete()
    {
        return $this->render('delete');
    }

}
