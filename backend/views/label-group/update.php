<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LabelGroup */

$this->title = Yii::t('adm', 'Update Label Group: {nameAttribute}', [
    'nameAttribute' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Label Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('adm', 'Update');
?>
<div class="label-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
