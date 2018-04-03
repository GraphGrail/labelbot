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

  ggEth.init(tokenContractAddress, expectedNetworkId, internalApi)  
    .catch(err => {
      console.log(err.code + ' ' + err)
      switch(err.code) {
        case 'ALREADY_INITIALIZED':
          return ggEth.getClientAddress()
        // TODO: обработка всех вариантов ошибок
        case 'NO_ACCOUNTS':
            return showEthClientError('Oops! Ethereum client has no accounts set')
        case 'NO_ETHEREUM_CLIENT':
            return showEthClientError('Oops! Ethereum client was not found')
        case 'INSUFFICIENT_TOKEN_BALANCE':
            return showEthClientError('Oops! Not enough tokens')
        default:
          return showEthClientError(err)
      }
    })
    .then(address => {
      console.log('User wallet address: ' + address)
      clientAddress = address
      $('.js-btn-transfer').attr('disabled', false)
    })
    .catch(err => {
      console.log(err)
    })


  $('.js-btn-transfer').on('click', e => {
    e.preventDefault();
    $('.js-btn-transfer').attr('disabled', true)

    ggEth.activeTransactionFinishedPromise()
      .then(_ => {
        return ggEth.transferTokensTo(contractAddress, tokensValue)
      })
      .catch(err => {
        console.log(err.code + ' ' + err)
        switch(err.code) {
          case 'TEST_ERROR':
            alert('error')
          // TODO: обработка всех вариантов ошибок
          default:
            alert(err)
        }
      })
      .then(_ => {
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
                    <input type="text" id="tokensValue" class="form-control m-input js-tokens-value" name="tokensValue" value="<?=$task->tokensNeededForContractActivation() ?>" disabled="disabled">
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
