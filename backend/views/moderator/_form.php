<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Moderator */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="moderator-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'auth_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'eth_addr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tg_chat_id')->textInput() ?>

    <?= $form->field($model, 'tg_id')->textInput() ?>

    <?= $form->field($model, 'tg_username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tg_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tg_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('adm', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
