<?php

use frontend\assets\pages\ScoreWorkAsset;
use yii\helpers\Html;
use frontend\assets\EthGatewayAsset;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $task \common\models\Task */
/* @var $sendingForm \frontend\models\SendScoreWorkForm */
/* @var $view \common\models\view\TaskDetailView */

ScoreWorkAsset::register($this);
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
      $('.js-btn-score-work').attr('disabled', false)
    })
    .catch(err => {
      showEthClientError(err)
    })
    
    $('.js-get-credit').on('click', e => {
		window.location = 'get-credit/' + clientAddress; 
	})


  $('.js-btn-score-work').on('click', e => {
    e.preventDefault();

    if (!$('.js-workers').val()) {
        alert('Score work to send results to blockchain')
        return false;
    }

    $('.js-btn-score-work').attr('disabled', true)

    let workers = JSON.parse($('.js-workers').val())

    console.log(workers)

    ggEth.activeTransactionFinishedPromise()
      .then(_ => {
        notifyCheckEthClient()
        return ggEth.scoreWork(contractAddress, workers)
      })
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
          case 'INSUFFICIENT_ETHER_BALANCE':
            return showEthCreditAlert();
          default:
            return showEthClientError(err)
        }
      })
      .then(_ => {
        $('.js-form').submit();
      })
  })

");

$this->registerJs("
  const finalizeInit = function() {
      const syncStatus = function() {
        $.post('". Url::toRoute(['task/sync-status', 'id' => $task->id]) . "', (response) => {
            console.log(response);
        })
      }
    
      const ggEth = graphGrailEther
      const contractAddress = $('.js-contract-address').val();
    
      $('.finalize-task-btn').on('click', function(e) {
        e.preventDefault();
    
        if (!clientAddress) {
            return;
        }
        $(this).attr('disabled', true)
        $('.m-portlet__head-caption').addClass('m-loader m-loader--success')
        
        ggEth.activeTransactionFinishedPromise()
          .then(_ => {    
            notifyCheckEthClient()       
            return ggEth.finalizeContract(contractAddress)
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
                return showEthClientError('Oops! Not enough tokens')
              default:
                return showEthClientError(err)
            }
          })
          .then(_ => {
            if (_ === false) {
                return
            }
            syncStatus()
            setTimeout(() => {window.location = '". Url::toRoute(['task/view', 'id' => $task->id]) . "' }, 3000);
          })
      })
  }();

");

?>
<h1>Jobs scoring</h1>


<?php $form = ActiveForm::begin([
        'options' => [
            'class'=>'m-section m--margin-bottom-5 score-work-form js-form',
        ],
    ]);

    echo $form->field($sendingForm, 'workers')
        ->hiddenInput([
            'class' => 'js-workers',
            'value' => '',
        ])
    ->label(false);


    foreach ($contractStatus->workers as $addr => $worker) {
        printf('<input type="hidden" class="workers-preview-url-%s" disabled="disabled" value="%s" />',
            $addr,
            Url::to(['task/preview-work',
                'id' => $task->id,
                'addr' => $addr,
            ])
        );
    }
?>
<div class="m-alert m-alert--icon alert alert-danger eth-errors" role="alert" style="display:none">
    <div class="m-alert__icon"><i class="flaticon-danger"></i></div>
    <div class="m-alert__text"></div>
</div>

<?=$this->render('_credit')?>

<input type="hidden" class="js-workers-source" disabled="disabled" value="<?=htmlspecialchars(json_encode($contractStatus->workers))?>" />
<div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    <?=Yii::t('app', "Sending data about jobs at \"$task->name\" task to blockchain.") ?>
                </h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
        </div>
    </div>
    <div class="m-portlet__body">
        <!--begin: Search Form -->
        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
            <div class="row align-items-center">
                <div class="col-xl-8 order-2 order-xl-1">
                    <div class="form-group m-form__group row align-items-center">
                        <div class="col-md-8">
                            <div class="m-form__group m-form__group--inline">
                                <div class="m-form__label">
                                    <label>
                                        <small>
                                            <?=Yii::t('app', 'Smart contract address') ?>:
                                        </small>
                                    </label>
                                </div>
                                <div class="m-form__control">
                                    <input type="text" id="address" class="form-control m-input js-contract-address" name="address" value="<?=$task->contract_address?>" disabled="disabled" />
                                </div>
                            </div>
                            <div class="d-md-none m--margin-bottom-10"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 order-1 m--align-right">
                    <?php if ($additionalActions = $view->getAdditionalActions()) {
                        ?>
                        <div class="pull-right m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push m--margin-left-10" data-dropdown-toggle="hover" aria-expanded="true">
                            <a href="#" class="m-portlet__nav-link btn btn-lg btn-secondary  m-btn m-btn--outline-2x m-btn--air m-btn--icon m-btn--icon-only m-btn--pill  m-dropdown__toggle">
                                <i class="la la-plus m--hide"></i>
                                <i class="la la-ellipsis-h"></i>
                            </a>
                            <div class="m-dropdown__wrapper">
                                <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 21.5px;"></span>
                                <div class="m-dropdown__inner">
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="m-nav">
                                                <li class="m-nav__section m-nav__section--first m--hide">
                                                    <span class="m-nav__section-text">
                                                        Quick Actions
                                                    </span>
                                                </li>
                                                <?php
                                                foreach ($additionalActions as $additionalAction) {
                                                    ?>
                                                    <li class="m-nav__item">
                                                        <a href="<?=$additionalAction->getUrl() ?: 'javascript: void(0);'?>" class="m-nav__link <?=$additionalAction->getOptions()['class']?>">
                                                            <i class="m-nav__link-icon <?=$additionalAction->getOptions()['iconClass']?>"></i>
                                                            <span class="m-nav__link-text">
                                                                <?=$additionalAction->getLabel()?>
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <span class="pull-right">
                        <?php
                            if ($action = $view->getNextAction()) {
                                ?>
                                <a href="<?=$action->getUrl() ?: 'javascript:void(0);'?>" class="<?=$action->getOptions()['class']?>  js-btn-release" style="margin-left: 10px;">
                                    <?=$action->getLabel()?>
                                </a>
                                <?php
                            }
                        ?>
                    </span>
                    <button type="submit" class="btn btn-info m-btn--pill m-btn--air pull-right js-btn-score-work <?=!$task->isPaused() ? 'disabled' : ''?>">
                        Send results to blockchain <i class="la la-send"></i>
                    </button>
                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                </div>
            </div>
        </div>
        <!--end: Search Form -->
        <!--begin: Datatable -->
        <div class="m_datatable" id="ajax_data"></div>
        <!--end: Datatable -->
    </div>
</div>
<?php ActiveForm::end() ?>

<div class="modal fade" id="delete_score_work_modal" tabindex="-1" role="dialog" aria-labelledby="delete_score_work_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Decline
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary break-decline-link" data-dismiss="modal">
                    No
                </button>
                <button type="button" class="btn btn-danger confirm-decline-link">
                    <i class="fa fa-trash-o"></i>
                    Yes
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-labelledby="preview_modal2" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Preview
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
