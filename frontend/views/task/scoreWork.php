<?php

use frontend\assets\pages\ScoreWorkAsset;
use yii\helpers\Html;
use frontend\assets\EthGatewayAsset;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $task \common\models\Task */
/* @var $sendingForm \frontend\models\SendScoreWorkForm */

ScoreWorkAsset::register($this);

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

');
?>
<h1>Jobs scoring</h1>


<?php $form = ActiveForm::begin([
        'options' => [
            'class'=>'m-section m--margin-bottom-5 score-work-form',
        ],
    ]);

    echo $form->field($sendingForm, 'workers')
        ->hiddenInput([
            'class' => 'form-control m-input js-workers',
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
<input type="hidden" class="form-control m-input js-workers-source" disabled="disabled" value="<?=htmlspecialchars(json_encode($contractStatus->workers))?>" />
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
                <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                    <button type="submit" class="btn btn-info m-btn--pill m-btn--air pull-right js-btn-score-work">
                        Send results to blockchain
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
