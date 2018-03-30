<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use frontend\assets\pages\ResetPasswordAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;

ResetPasswordAsset::register($this);

$errorSummaryHeader = '<div class="m-alert m-alert--outline alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><div class="auth-form-err-message-container"><span>';
$errorSummaryFooter = '</span></div></div>';

?>
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2 m-login--forget-password" id="m_login" style="background-image: url(/images/bg/bg-3.jpg);">
        <div class="m-grid__item m-grid__item--fluid m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__logo">
                    <a href="#">
                        <img src="/images/logo/graphgrail.png">
                    </a>
                </div>
                <div class="m-login__forget-password">
                    <div class="m-login__head">
                        <h3 class="m-login__title">
                            <?=$this->title?>
                        </h3>
                        <div class="m-login__desc">
                            Please choose your new password
                        </div>
                    </div>
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'reset-password-form',
                        'options' => [
                            'class' => 'm-login__form m-form',
                        ],
                        'fieldConfig' => [
                            'options' => [
                                'tag' => false,
                            ],
                        ],
                    ]);
                    $form->errorSummaryCssClass = 'custom-error-summary';

                    echo $form->errorSummary($model, [
                        'header' => $errorSummaryHeader,
                        'footer' => $errorSummaryFooter,
                    ]);
                    ?>
                    <div class="form-group m-form__group">
                        <?= $form->field($model, 'password', ['errorOptions' => ['class' => 'hidden'],])
                            ->passwordInput([
                                'class' => 'form-control m-input m-login__form-input--last',
                                'placeholder' => $model->getAttributeLabel('password'),
                            ])
                            ->label(false) ?>
                    </div>
                    <div class="m-login__form-action">
                        <?=Html::submitButton('Save', [
                            'id' => 'm_login_forget_password_submit',
                            'class' => 'btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn m-login__btn--primary',
                        ])?>
                        &nbsp;&nbsp;
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
