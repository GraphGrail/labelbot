<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Moderator */

$this->title = Yii::t('adm', 'Update Moderator: {nameAttribute}', [
    'nameAttribute' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Moderators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('adm', 'Update');
?>
<div class="moderator-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
