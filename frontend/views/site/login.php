<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $loginForm \common\models\LoginForm */
/* @var $signUpForm \frontend\models\SignupForm */
/* @var $urls array */

use frontend\assets\LoginAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

LoginAsset::register($this);

$errorSummaryHeader = '<div class="m-alert m-alert--outline alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><span>';
$errorSummaryFooter = '</span></div>';

?>
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2" id="m_login" style="background-image: url(/images/bg/bg-3.jpg);">
        <div class="m-grid__item m-grid__item--fluid	m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__logo">
                    <a href="#">
                        <img src="/images/logo/graphgrail.png">
                    </a>
                </div>
                <div class="m-login__signin">
                    <div class="m-login__head">
                        <h3 class="m-login__title">
                            Sign In
                        </h3>
                    </div>
                    <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
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

                        echo $form->errorSummary($loginForm, [
                            'header' => $errorSummaryHeader,
                            'footer' => $errorSummaryFooter,
                        ]);
                    ?>

                        <div class="form-group m-form__group">
                            <?= $form->field($loginForm, 'username', ['errorOptions' => ['class' => 'hidden'],])
                                ->textInput([
                                    'autofocus' => true,
                                    'class' => 'form-control m-input',
                                    'placeholder' => $loginForm->getAttributeLabel('username'),
                                    'errorOptions' => ['tag' => null],
                                ])
                                ->error(['tag' => null])
                                ->label(false) ?>
                        </div>
                        <div class="form-group m-form__group">
                            <?= $form->field($loginForm, 'password', ['errorOptions' => ['class' => 'hidden'],])
                                ->passwordInput([
                                    'class' => 'form-control m-input m-login__form-input--last',
                                    'placeholder' => $loginForm->getAttributeLabel('password'),
                                ])
                                ->label(false) ?>
                        </div>
                        <div class="row m-login__form-sub">
                            <div class="col m--align-left m-login__form-left">
                                <label class="m-checkbox  m-checkbox--focus">
                                    <?= $loginForm->getAttributeLabel('rememberMe');?>
                                    <?= $form->field($loginForm, 'rememberMe', ['errorOptions' => ['class' => 'hidden'],])
                                        ->checkbox([
                                            'name' => 'remember',
                                            'label' => false,
                                        ], false);
                                    ?>
                                    <span></span>
                                </label>
                            </div>
                            <div class="col m--align-right m-login__form-right">
                                <a href="javascript:;" id="m_login_forget_password" class="m-link">
                                    Forget Password ?
                                </a>
                            </div>
                        </div>
                        <div class="m-login__form-action">
                            <?= Html::button('Sign In', [
                                    'class' => 'btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary',
                                    'id' => 'm_login_signin_submit',
                                ]) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="m-login__signup">
                    <div class="m-login__head">
                        <h3 class="m-login__title">
                            Sign Up
                        </h3>
                        <div class="m-login__desc">
                            Enter your details to create your account:
                        </div>
                    </div>
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'form-signup',
                        'action' => $urls['signup'],
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

                    echo $form->errorSummary($signUpForm, [
                        'header' => $errorSummaryHeader,
                        'footer' => $errorSummaryFooter,
                    ]);
                    ?>
                        <div class="form-group m-form__group">
                            <?= $form->field($signUpForm, 'username', ['errorOptions' => ['class' => 'hidden'],])
                                ->textInput([
                                    'autofocus' => true,
                                    'class' => 'form-control m-input',
                                    'placeholder' => $signUpForm->getAttributeLabel('username'),
                                    'errorOptions' => ['tag' => null],
                                ])
                                ->error(['tag' => null])
                                ->label(false) ?>
                        </div>
                        <div class="form-group m-form__group">
                            <?= $form->field($signUpForm, 'email', ['errorOptions' => ['class' => 'hidden'],])
                                ->textInput([
                                    'class' => 'form-control m-input',
                                    'placeholder' => $signUpForm->getAttributeLabel('email'),
                                    'errorOptions' => ['tag' => null],
                                ])
                                ->error(['tag' => null])
                                ->label(false) ?>
                        </div>
                        <div class="form-group m-form__group">
                            <?= $form->field($signUpForm, 'password', ['errorOptions' => ['class' => 'hidden'],])
                                ->passwordInput([
                                    'class' => 'form-control m-input m-login__form-input--last',
                                    'placeholder' => $signUpForm->getAttributeLabel('password'),
                                ])
                                ->label(false) ?>
                        </div>
                        <div class="form-group m-form__group">
                            <?= $form->field($signUpForm, 'password_confirm', ['errorOptions' => ['class' => 'hidden'],])
                                ->passwordInput([
                                    'class' => 'form-control m-input m-login__form-input--last',
                                    'placeholder' => $signUpForm->getAttributeLabel('password_confirm'),
                                ])
                                ->label(false) ?>
                        </div>
                        <div class="m-login__form-action">
                            <button id="m_login_signup_submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn">
                                Sign Up
                            </button>
                            &nbsp;&nbsp;
                            <button id="m_login_signup_cancel" class="btn btn-outline-focus m-btn m-btn--pill m-btn--custom  m-login__btn">
                                Cancel
                            </button>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="m-login__forget-password">
                    <div class="m-login__head">
                        <h3 class="m-login__title">
                            Forgotten Password ?
                        </h3>
                        <div class="m-login__desc">
                            Enter your email to reset your password:
                        </div>
                    </div>
                    <form class="m-login__form m-form" action="">
                        <div class="form-group m-form__group">
                            <input class="form-control m-input" type="text" placeholder="Email" name="email" id="m_email" autocomplete="off">
                        </div>
                        <div class="m-login__form-action">
                            <button id="m_login_forget_password_submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn m-login__btn--primaryr">
                                Request
                            </button>
                            &nbsp;&nbsp;
                            <button id="m_login_forget_password_cancel" class="btn btn-outline-focus m-btn m-btn--pill m-btn--custom m-login__btn">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
                <div class="m-login__account">
							<span class="m-login__account-msg">
								Don't have an account yet ?
							</span>
                    &nbsp;&nbsp;
                    <a href="javascript:;" id="m_login_signup" class="m-link m-link--light m-login__account-link">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
