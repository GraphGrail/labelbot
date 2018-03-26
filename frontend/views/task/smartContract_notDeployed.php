<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
$this->registerJs("
	const ggEth = graphGrailEther
	const tokenContractAddress = '0x11e0892806ab9fd37224a2031c51156968c2ee72'
	const expectedNetworkId = 4 // Rinkeby
	const internalApi = 'http://tgbot.test/api'
	let clientAddress

	ggEth.init(tokenContractAddress, expectedNetworkId, internalApi)  
		.catch(err => {
			console.log(err.code + ' ' + err)
			switch(err.code) {
				case 'ALREADY_INITIALIZED':
					return ggEth.getClientAddress()
				// TODO: обработка всех вариантов ошибок
				default:
					alert(err)
			}
		})
		.then(address => {
			console.log('User wallet address: ' + address)
			clientAddress = address
			$('.js-address').val(address)
			return ggEth.checkBalances(address)
		})
		.then(balances => {
			if (balances.ether == 0 || balances.token == 0) {
				$('.js-credit-invitation').show()
			} else {
				$('.js-btn-create').css('display', 'block')
			}
		})
		.catch(err => {
			console.log(err)
		})

	$('.js-get-credit').on('click', e => {
		window.location = 'get-credit/' + clientAddress; 
	})


");
?>
<h1>Smart-contract for task "<?=$task->name ?>"</h1>

<div class="row">
  <div class="col-lg-8">

  	<div class="m-alert m-alert--icon alert alert-danger js-credit-invitation" role="alert">
	  <div class="m-alert__icon">
		<i class="flaticon-danger"></i>
	  </div>
	  <div class="m-alert__text">
		<?=Yii::t('app', 'You haven\'t enough ethereum or tokens in your wallet. Please, get free credit.') ?>
	  </div>
	  <div class="m-alert__actions" style="width: 220px;">
		<?=Html::tag('button', 'Get credit', ['class' => 'btn btn-link btn-outline-light btn-sm m-btn m-btn--hover-secondary js-get-credit']) ?>
	  </div>
	</div>

    <div class="m-portlet m-portlet--tab">
      <div class="m-portlet__body m-portlet__body--no-padding">
        <div class="row m-row--no-padding m-row--col-separator-xl">
          <div class="col-md-12">
            <div class="m-widget1">
              <div class="m--padding-bottom-5"></div>
              <h5 class="m-widget5__title m--margin-bottom-25">
              	<?=Yii::t('app', 'Create Smart-contract') ?>
              </h5>
			  <form id="w0" class="m-section m--margin-bottom-5" action="" method="post">
			  	<input type="hidden" name="<?=Yii::$app->request->csrfParam ?>" value="<?=Yii::$app->request->getCsrfToken() ?>" />
			    <div class="form-group m-form__group">
			    <div class="m-section__sub">
                	<?=Yii::t('app', 'Smart contract will be created for this Ethereum wallet address') ?>
                </div>
		        <div class="form-group field-task-label_group_id required">
		        	<input type="hidden" id="address" class="form-control m-input js-address" name="address">
					<input type="text" class="form-control m-input js-address" disabled="disabled">
					<div class="help-block"></div>
				</div>                  
			      </div>
			        <div class="form-group m-form__group m--padding-top-20">
			          <button type="submit" class="btn btn-info m-btn--pill m-btn--air pull-right js-btn-create" disabled="disabled">Create smart contract</button>                  
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
