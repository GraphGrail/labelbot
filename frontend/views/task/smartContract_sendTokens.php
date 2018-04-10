<?php

use yii\helpers\Html;
use frontend\assets\EthGatewayAsset;

/* @var $this yii\web\View */
EthGatewayAsset::register($this);

$this->registerJs("
  const ggEth = graphGrailEther
  const tokenContractAddress = '" . Yii::$app->params['tokenContractAddress'] . "'
  const expectedNetworkId = '" . Yii::$app->params['networkId'] . "'
  const internalApi = '" . Yii::$app->params['ethGatewayApiUrl'] . "'

  let clientAddress
  const contractAddress = $('.js-contract-address').val();
  const tokensValue = $('.js-tokens-value').val();

  ggEth.init(tokenContractAddress, expectedNetworkId)  
    .catch(err => {
      console.log(err.code + ' ' + err)
      switch(err.code) {
        case 'ALREADY_INITIALIZED':
          return ggEth.getClientAddress()
        case 'NO_ACCOUNTS':
            return showEthClientError('Oops! Ethereum client not logged in. Log in and reload page')
        case 'NO_ETHEREUM_CLIENT':
            return showEthClientError('Oops! Ethereum client was not found. Install one, such as Metamask and reload page')
        case 'WRONG_NETWORK':
            return showEthClientError('Oops! Etherium client select wrong network. Change it and reload page')       
        default:
          return showEthClientError(err)
      }
    })
    .then(address => {
      console.log('User wallet address: ' + address)
      clientAddress = address
      $('.js-btn-transfer').attr('disabled', false)
      return ggEth.checkBalances(address)
    })
    .then(balances => {
        if (!balances) {
            return;
        }      
        console.log('Ether: ' + balances.ether + ', tokens: ' + balances.ether)
        if (balances.ether == 0 || balances.token == 0) {
            showEthCreditAlert()
        }
    })
    .catch(err => {
      console.log(err)
    })

    $('.js-get-credit').on('click', e => {
		window.location = 'get-credit/' + clientAddress; 
	})

  $('.js-btn-transfer').on('click', e => {
    e.preventDefault();
    $('.js-btn-transfer').attr('disabled', true).addClass('m-loader m-loader--right')

    ggEth.activeTransactionFinishedPromise()
      .then(_ => {
        notifyCheckEthClient()
        return ggEth.transferTokensTo(contractAddress, tokensValue)
      })
      .catch(err => {
        console.log(err.code + ' ' + err)
        switch(err.code) {
          case 'NOT_INITIALIZED':
            return showEthClientError('Oops! Etherium client was not initialized. Please reload page')
          case 'TRANSACTION_ALREADY_RUNNING':
            return showEthClientError('Oops! Transaction already running. Reload page')
          case 'INSUFFICIENT_ETHER_BALANCE':
            return showEthCreditAlert();
          case 'INSUFFICIENT_TOKEN_BALANCE':
            return showEthCreditAlert();
          default:
            return showEthClientError(err)
        }
      })
      .then(_ => {
        if (_ === false) {
            return
        }
        $('.js-form').submit();
      })
  })

");
?>
<h1>Now you need to send tokens to smart contract.</h1>

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
              	<?=Yii::t('app', "Smart-contract for task \"$task->name\" deployed to blockchain.") ?>
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
                    <input type="text" id="tokensValue" class="form-control m-input js-tokens-value" name="tokensValue" value="<?=$tokensValue ?>" disabled="disabled">
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
