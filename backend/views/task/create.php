<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Task */

$this->title = Yii::t('adm', 'Create Task');
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
