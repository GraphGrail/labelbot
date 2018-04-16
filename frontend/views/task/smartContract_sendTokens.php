<?php

/* @var $this yii\web\View */

?>
<h1>Now you need to send GAI tokens to smart contract.</h1>

<div class="row">
  <div class="col-lg-8">

      <div class="m-alert m-alert--icon alert alert-danger eth-errors" role="alert" style="display:none">
          <div class="m-alert__icon"><i class="flaticon-danger"></i></div>
          <div class="m-alert__text"></div>
      </div>

      <?=$this->render('_credit')?>

    <div class="m-portlet m-portlet--tab">
      <div class="m-portlet__body m-portlet__body--no-padding">
        <div class="row m-row--no-padding m-row--col-separator-xl">
          <div class="col-md-12">
            <div class="m-widget1">
              <div class="m--padding-bottom-5"></div>
              <h5 class="m-widget5__title m--margin-bottom-25">
              	<?=Yii::t('app', "Smart-contract for task \"$task->name\" succesfully deployed to blockchain.<br>Now you need to send GAI tokens to credit smart contract") ?>
              </h5>
      			  <form class="m-section m--margin-bottom-5 js-form" method="post" action="">
      			  	<input type="hidden" name="<?=Yii::$app->request->csrfParam ?>" value="<?=Yii::$app->request->getCsrfToken() ?>" />
                <input type="hidden" class="form-control m-input" name="action" value="sendTokens">
                <div class="form-group m-form__group">
                  <div class="m-section__sub">
                    <?=Yii::t('app', 'Smart contract address') ?>
                  </div>
                  <div class="form-group field-task-label_group_id required">
                    <input type="text" id="address" class="form-control m-input js-contract-address" name="address" value="<?=$task->contract_address ?>" disabled="disabled">
                    <div class="help-block"></div>
                  </div>                  
                </div>
                <div class="form-group m-form__group">
                  <div class="m-section__sub">
                    <?=Yii::t('app', 'Tokens to send') ?>
                  </div>
                  <div class="form-group field-task-label_group_id required">
                      <input type="hidden" id="tokensValue" class="form-control m-input js-tokens-value" name="tokensValue" value="<?=$tokensValue ?>" disabled="disabled">
                      <input type="text" id="tokensValueFormatted" class="form-control m-input" name="tokensValueFormatted" value="<?=bcdiv($tokensValue,
                          '1000000000000000000',
                          6
                      ); ?>" disabled="disabled">
                    <div class="help-block"></div>
                  </div>                  
                </div>
      			    <div class="form-group m-form__group m--padding-top-20">                  
                  <button type="submit" class="btn btn-info m-btn--pill m-btn--air pull-right js-btn-transfer" disabled="disabled">Send tokens</button>                  
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
