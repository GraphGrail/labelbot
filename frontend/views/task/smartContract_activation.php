<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
$this->registerJs("

");
?>
<h1>You need to activate smart-contract.</h1>

<div class="row">
  <div class="col-lg-8">
    <div class="m-portlet m-portlet--tab">
      <div class="m-portlet__body m-portlet__body--no-padding">
        <div class="row m-row--no-padding m-row--col-separator-xl">
          <div class="col-md-12">
            <div class="m-widget1">
              <div class="m--padding-bottom-5"></div>
              <h5 class="m-widget5__title m--margin-bottom-25">
              	<?=Yii::t('app', "Smart-contract for task \"$task->name\" deployed to blockchain.") ?>
              </h5>
			  <form id="w0" class="m-section m--margin-bottom-5" action="" method="post">
			  	<input type="hidden" name="<?=Yii::$app->request->csrfParam ?>" value="<?=Yii::$app->request->getCsrfToken() ?>" />
			    <div class="form-group m-form__group">
			    <div class="m-section__sub">
                	<?=Yii::t('app', 'Smart contract address') ?>
                </div>
		        <div class="form-group field-task-label_group_id required">
					<input type="text" id="address" class="form-control m-input js-address" name="address" value="<?=$task->contract_address ?>" disabled="disabled">
					<div class="help-block"></div>
				</div>                  
			      </div>
			        <div class="form-group m-form__group m--padding-top-20">
			          <button type="submit" class="btn btn-info m-btn--pill m-btn--air pull-right js-btn-create" disabled="disabled">Activate smart contract</button>                  
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
