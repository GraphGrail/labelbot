<?php

use yii\helpers\Html;
use frontend\assets\EthGatewayAsset;

/* @var $this yii\web\View */
/*EthGatewayAsset::register($this);

$this->registerJs("
  const ggEth = graphGrailEther
  const tokenContractAddress = '" . Yii::$app->params['tokenContractAddress'] . "'
  const expectedNetworkId = '" . Yii::$app->params['networkId'] . "'
  const internalApi = '" . Yii::$app->params['ethGatewayApiUrl'] . "'

  let clientAddress
  const contractAddress = $('.js-contract-address').val();

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
      $('.js-btn-score-work').attr('disabled', false)
    })
    .catch(err => {
      console.log(err)
    })


  $('.js-btn-score-work').on('click', e => {
    e.preventDefault();
    $('.js-btn-score-work').attr('disabled', true)

    let workers = JSON.parse($('.js-workers').val())

    console.log(workers)

    ggEth.activeTransactionFinishedPromise()
      .then(_ => {
        return ggEth.scoreWork(contractAddress, workers)
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
      //  $('.js-form').submit();
      })
  })

");*/

$this->registerJs('

let workers = {
    "0x13fb25c0e3c3a2c4bd84388cc1d36648f921e151":{"totalItems":5,"approvedItems":2,"declinedItems":1},
    "0x23fb25c0e3c3a2c4bd84388cc1d36648f921e152":{"totalItems":2,"approvedItems":2,"declinedItems":0},
    "0x33fb25c0e3c3a2c4bd84388cc1d36648f921e153":{"totalItems":8,"approvedItems":2,"declinedItems":3}
  }




');
?>
<h1>Jobs scoring</h1>

<div class="row">
  <div class="col-lg-8">
    <div class="m-portlet m-portlet--tab">
      <div class="m-portlet__body m-portlet__body--no-padding">
        <div class="row m-row--no-padding m-row--col-separator-xl">
          <div class="col-md-12">
            <div class="m-widget1">
              <div class="m--padding-bottom-5"></div>
              <h5 class="m-widget5__title m--margin-bottom-25">
              	<?=Yii::t('app', "Sending data about jobs at \"$task->name\" task to blockchain.") ?>
              </h5>
      			  <form class="m-section m--margin-bottom-5 js-form" method="post" action="">
      			  	<input type="hidden" name="<?=Yii::$app->request->csrfParam ?>" value="<?=Yii::$app->request->getCsrfToken() ?>" />
                <input type="hidden" class="form-control m-input" name="action" value="sendTokens">
                <div class="form-group m-form__group">
                  <div class="m-section__sub">
                    <?=Yii::t('app', 'Smart contract address') ?>
                  </div>
                  <div class="form-group field-task-label_group_id required">
                    <input type="text" id="address" class="form-control m-input js-contract-address" name="address" value="<?/*=$task->contract_address*/ ?>" disabled="disabled">
                    <input type="hidden" class="form-control m-input js-workers" disabled="disabled"><?/*=json_encode($contractStatus->workers)*/ ?></input>
                    <div class="help-block"></div>
                  </div>                  
                </div>
                <div class="form-group m-form__group">
                  <input class="form-control m-input js-workers" name="score"><?/*=json_encode($contractStatus->workers)*/ ?></input>
                </div>
      			    <div class="form-group m-form__group m--padding-top-20">                  
                  <button type="submit" class="btn btn-info m-btn--pill m-btn--air pull-right js-btn-score-work" disabled="disabled">Send results to blockchain</button>                  
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
