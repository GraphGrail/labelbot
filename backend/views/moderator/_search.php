<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ModeratorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="moderator-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'auth_token') ?>

    <?= $form->field($model, 'eth_addr') ?>

    <?= $form->field($model, 'tg_chat_id') ?>

    <?= $form->field($model, 'tg_id') ?>

    <?php // echo $form->field($model, 'tg_username') ?>

    <?php // echo $form->field($model, 'tg_first_name') ?>

    <?php // echo $form->field($model, 'tg_last_name') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'current_task') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('adm', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('adm', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
