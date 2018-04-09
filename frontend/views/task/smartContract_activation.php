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
      $('.js-btn-activate').attr('disabled', false)
    })
    .catch(err => {
      console.log(err)
    })

  $('.js-btn-activate').on('click', e => {
    e.preventDefault();
    $('.js-btn-activate').attr('disabled', true).addClass('m-loader m-loader--right')

    ggEth.activeTransactionFinishedPromise()
      .then(_ => {
        notifyCheckEthClient()
        return ggEth.activateContract(contractAddress)
      })
      .catch(err => {
        console.log(err.code + ' ' + err)
        switch(err.code) {
//          case 'INVALID_CONTRACT_STATE':
//            return true;          
          case 'INSUFFICIENT_TOKEN_BALANCE':
            return showEthClientError('Oops! Not enough tokens')
          case 'NOT_INITIALIZED':
            return  showEthClientError('Oops! Etherium client was not initialized. Please reload page')
          case 'TRANSACTION_ALREADY_RUNNING':
            return showEthClientError('Oops! Transaction already running. Reload page') 
          case 'CONTRACT_NOT_FOUND':
            return showEthClientError('Oops! Contract not found') 
          case 'INSUFFICIENT_ETHER_BALANCE':
            return showEthClientError('Oops! Not enough ether') 
          case 'INVALID_CONTRACT_STATE':
            return showEthClientError('Oops! Invalid contract state') 
          case 'UNAUTHORIZED':
            return showEthClientError('Oops! Unauthorized. Check permissions') 
          case 'TRANSACTION_FAILED':
            return showEthClientError('Oops! Transaction failed')
          default:
            return showEthClientError(err)
        }
      })
      .then(_ => {
        if(_ === false) {
          return;
        } 
        $('.js-form').submit();
      })
  })

");
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
                	<?=Yii::t('app', "Smart-contract for task \"$task->name\" deployed to blockchain.") ?>
                </h5>
        			  <form class="m-section m--margin-bottom-5 js-form" method="post" action="">
        			  	<input type="hidden" name="<?=Yii::$app->request->csrfParam ?>" value="<?=Yii::$app->request->getCsrfToken() ?>" />
                  <input type="hidden" class="form-control m-input" name="action" value="activation">
        			    <div class="form-group m-form__group">
          			    <div class="m-section__sub">
                      <?=Yii::t('app', 'Smart contract address') ?>
                    </div>
          		      <div class="form-group field-task-label_group_id required">
          					  <input type="text" id="address" class="form-control m-input js-contract-address" name="address" value="<?=$task->contract_address ?>" disabled="disabled">
          					  <div class="help-block"></div>
          				  </div>                  
        			    </div>
    			        <div class="form-group m-form__group m--padding-top-20">
    			          <button class="btn btn-danger m-btn--pill m-btn--air pull-right js-btn-activate" disabled="disabled">Activate smart contract</button>        
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
