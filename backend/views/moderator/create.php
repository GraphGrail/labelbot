<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Moderator */

$this->title = Yii::t('adm', 'Create Moderator');
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Moderators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moderator-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
