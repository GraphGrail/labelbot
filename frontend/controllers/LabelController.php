<?php

namespace frontend\controllers;

use common\models\LabelGroup;
use common\components\LabelsTree;
use yii\filters\AccessControl;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class LabelController extends \yii\web\Controller
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

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionNew()
    {
        $model = new LabelGroup();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            $model->status = LabelGroup::STATUS_NO_LABELS_TREE;
            if (!$model->save()) {
                throw new ServerErrorHttpException('LabelGroup Save error');
            }

            $labelsTree = new LabelsTree($model);

            if (!$labelsTree->validate()) {
                throw new BadRequestHttpException('Cant decode labels tree');
            }

            if (!$labelsTree->create()) {
                $model->status = LabelGroup::STATUS_LABELS_TREE_ERROR;
                $model->save();
                throw new ServerErrorHttpException('Cant create labels tree');
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
