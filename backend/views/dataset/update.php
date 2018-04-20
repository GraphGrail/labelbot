<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Dataset */

$this->title = Yii::t('adm', 'Update Dataset: {nameAttribute}', [
    'nameAttribute' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Datasets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('adm', 'Update');
?>
<div class="dataset-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
