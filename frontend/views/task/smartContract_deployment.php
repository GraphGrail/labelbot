<?php

/* @var $this yii\web\View */

?>
<h1>Smart-contract for task "<?= $task->name ?>"</h1>

<div class="row">
    <div class="col-lg-8">

        <div class="m-alert m-alert--icon alert alert-danger eth-errors" role="alert" style="display:none">
            <div class="m-alert__icon"><i class="flaticon-danger"></i></div>
            <div class="m-alert__text"></div>
        </div>

        <?= $this->render('_credit') ?>

        <div class="m-portlet m-portlet--tab">
            <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="row m-row--no-padding m-row--col-separator-xl">
                    <div class="col-md-12">
                        <div class="m-widget1">
                            <div class="m--padding-bottom-5"></div>
                            <h5 class="m-widget5__title m--margin-bottom-25">
                                <?= Yii::t('app', 'Now we ready to create smart-contract for the task you created on the previous step.') ?>
                            </h5>
                            <p class="m-widget5__title m--margin-bottom-25">
                                <?= Yii::t('app',
                                    'To get task available to participants of the GraphGrailAi platform you need deploy it to the testnet Ethereum blockchain (Rinkeby).<br>
                    This guarantee for you as client and for data-labeller that all work will be transparent for both sides.') ?>
                            </p>
                            <form class="m-section m--margin-bottom-5" action="" method="post">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                       value="<?= Yii::$app->request->getCsrfToken() ?>"/>
                                <div class="form-group m-form__group">
                                    <div class="m-section__sub">
                                        <?= Yii::t('app', 'Smart contract will be created for this Ethereum wallet address') ?>
                                    </div>
                                    <div class="form-group field-task-label_group_id required">
                                        <input type="hidden" id="address" class="form-control m-input js-address"
                                               name="address">
                                        <input type="text" class="form-control m-input js-address" disabled="disabled">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="form-group m-form__group m--padding-top-20">
                                    <button type="submit"
                                            class="btn btn-info m-btn--pill m-btn--air pull-right js-btn-create"
                                            disabled="disabled">Create smart contract
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                            <div class="m--padding-bottom-20"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
