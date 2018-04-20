<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LabelGroup */

$this->title = Yii::t('adm', 'Create Label Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Label Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="label-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
