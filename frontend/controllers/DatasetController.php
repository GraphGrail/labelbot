<?php

namespace frontend\controllers;

use common\models\Dataset;
use frontend\models\UploadDatasetForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Yii;

class DatasetController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
     * @return string
     */
    public function actionIndex()
    {
        $datasets = Dataset::find()
            ->ownedByUser()
            ->undeleted()
            ->orderBy(['id' => SORT_DESC])
            ->all();
        return $this->render('index', [
            'datasets' => $datasets
        ]);
    }

    /**
     * New Dataset Uploading
     * @return string
     */
    public function actionNew()
    {
        $model = new UploadDatasetForm();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->datasetFile = UploadedFile::getInstance($model, 'datasetFile');

            if ($model->upload()) {
                // file is uploaded successfully
                $this->redirect('index');
            }
        }

        return $this->render('new', ['model' => $model]);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        if (!$model = Dataset::findOne($id)) {
            throw new NotFoundHttpException(sprintf('Dataset with id `%s` not found', $id));
        }
        $model->delete();
        return $this->asJson([
            'success' => $model->deleted,
        ]);
    }

}
