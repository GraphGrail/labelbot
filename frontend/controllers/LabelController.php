<?php

namespace frontend\controllers;

use common\models\Label;
use common\models\LabelGroup;
use common\components\LabelsTree;
use yii\helpers\StringHelper;
use yii\filters\AccessControl;
use Yii;
use yii\web\NotFoundHttpException;

class LabelController extends \yii\web\Controller
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
     * Show Labels list
     * @return string
     */
    public function actionIndex()
    {
        $labelGroups = LabelGroup::find()
            ->ownedByUser()
            ->undeleted()
            ->orderBy(['id' => SORT_DESC])
            ->all();
        return $this->render('index', [
            'labelGroups' => $labelGroups
        ]);
    }

    public function actionNew()
    {
        $model = new LabelGroup();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            $model->status = LabelGroup::STATUS_NO_LABELS_TREE;
            if (!$model->save()) {
                throw new \Exception('LabelGroup Save error');
            }

            $labelsTree = new LabelsTree($model);

            // TODO: needs static method or vlidator to make validation before model created
            if (!$labelsTree->validate()) {
                throw new \Exception('Cant decode labels tree');
            }

            if (!$labelsTree->create()) {
                // TODO: updateStatus trait
                $model->status = LabelGroup::STATUS_LABELS_TREE_ERROR;
                $model->save();

                // TODO: set error and error message for LabelGroup
                throw new \Exception('Cant create labels tree');
            }

            $model->status = LabelGroup::STATUS_OK;
            $model->save();

            $this->redirect('index');
        }

        return $this->render('new', ['model' => $model]);
    }

    /**
     * @param $id
     * @return string
     * @throws \yii\db\StaleObjectException
     * @throws \Exception
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        if (!$model = LabelGroup::findOne($id)) {
            throw new NotFoundHttpException(sprintf('Label group with id `%s` not found', $id));
        }
        $model->delete();
        return $this->asJson([
            'success' => $model->deleted,
        ]);
    }
}
