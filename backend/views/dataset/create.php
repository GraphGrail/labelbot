<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Dataset */

$this->title = Yii::t('adm', 'Create Dataset');
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Datasets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dataset-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
