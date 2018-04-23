<?php

/* @var $this yii\web\View */

?>
<h1>You need to activate smart-contract.</h1>

<div class="row">
    <div class="col-lg-8">

        <div class="m-alert m-alert--icon alert alert-danger eth-errors" role="alert" style="display:none">
            <div class="m-alert__icon"><i class="flaticon-danger"></i></div>
            <div class="m-alert__text"></div>
        </div>

        <div class="m-portlet m-portlet--tab">
            <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="row m-row--no-padding m-row--col-separator-xl">
                    <div class="col-md-12">
                        <div class="m-widget1">
                            <div class="m--padding-bottom-5"></div>
                            <h5 class="m-widget5__title m--margin-bottom-25">
                                <?= Yii::t('app', "Now you can activate contract to start tasks distribution or you can cancel it and get tokens back.") ?>
                            </h5>
                            <form class="m-section m--margin-bottom-5 js-form" method="post" action="">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                       value="<?= Yii::$app->request->getCsrfToken() ?>"/>
                                <input type="hidden" class="form-control m-input" name="action" value="activation">
                                <?= \common\widgets\smartContractAddress::widget(['address'=>$task->contract_address]); ?>
                                <div class="form-group m-form__group m--padding-top-20">
                                    <?php /* <button class="btn btn-outline-info m-btn--pill pull-right finalize-task-btn"
                                            data-id="<?=$task->id?>"
                                            data-contract-address="<?=$task->contract_address?>"
                                            disabled="disabled">Cancel
                                    </button> */ ?>
                                    <button class="btn btn-danger m-btn--pill m-btn--air pull-right js-btn-activate"
                                            disabled="disabled">Activate smart contract
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
