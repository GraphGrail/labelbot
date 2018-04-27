<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Label */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="label-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_label_id')->textInput() ?>

    <?= $form->field($model, 'text')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'label_group_id')->textInput() ?>

    <?= $form->field($model, 'ordering')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('adm', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
