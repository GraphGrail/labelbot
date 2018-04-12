<?php

use common\models\view\TaskDetailView;
use frontend\assets\EthGatewayAsset;
use frontend\assets\pages\TaskPageAsset;
use yii\helpers\Url;

TaskPageAsset::register($this);
EthGatewayAsset::register($this);

/* @var $this yii\web\View */
/* @var $tasks \common\models\Task[] */

$this->title = 'Tasks';


$this->registerJs("
  const syncStatus = function(taskId) {
    $.post('task/' + taskId + '/sync-status', (response) => {
        console.log(response);
    })
  }
  const finalizeInit = function() {
      const tokenContractAddress = '" . Yii::$app->params['tokenContractAddress'] . "'
      const expectedNetworkId = '" . Yii::$app->params['networkId'] . "'
    
      let clientAddress
      const contractAddress = $('.js-contract-address').val();
      
      graphGrailEther.init(tokenContractAddress, expectedNetworkId)
        .catch(err => {
            console.log(err.code + ' ' + err);
            switch(err.code) {
                case 'ALREADY_INITIALIZED':
                    return graphGrailEther.getClientAddress();
                case 'INVALID_ETHEREUM_ADDRESS':
                    return showEthClientError(err);
                case 'NO_ACCOUNTS':
                    return showEthClientError('Oops! Ethereum client not logged in. Log in and reload page')
                case 'NO_ETHEREUM_CLIENT':
                    return showEthClientError('Oops! Ethereum client was not found. Install one, such as Metamask and reload page')
                case 'WRONG_NETWORK':
                    return showEthClientError('Oops! Ethereum client was not found. Install one, such as <a href=\"https://metamask.io/\" target=\"_blank\">Metamask</a> and reload page')
                default:
                    return showEthClientError(err)
            }
        })
        .then(address => {
             clientAddress = address
        })
        .catch(err => {
            console.log(err);
            showEthClientError(err)
        })
    
  
      $('.finalize-task-btn').on('click', function(e) {
        e.preventDefault();
    
        var taskId = $(this).data('id');
        var contractAddress = $(this).data('contract-address');
        if (!contractAddress) {
            return;
        }
        if (!clientAddress) {
            return;
        }
       
        $(this).attr('disabled', true).addClass('m-loader m-loader--right')
        graphGrailEther.activeTransactionFinishedPromise()
          .then(_ => {
            notifyCheckEthClient()
            return graphGrailEther.finalizeContract(contractAddress)
          })
          .catch(err => {
            console.log(err.code + ' ' + err)
            switch(err.code) {
              case 'NOT_INITIALIZED':
                return showEthClientError('Oops! Etherium client was not initialized. Please reload page')
              case 'TRANSACTION_ALREADY_RUNNING':
                return showEthClientError('Oops! Transaction already running. Reload page')
              case 'INSUFFICIENT_ETHER_BALANCE':
                return showEthCreditAlert(taskId, clientAddress)
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
            syncStatus(taskId)
            setTimeout(() => {window.location.reload()}, 3000);
          })
      })
  }();

");
?>
<div class="row">
    <div class="col-xl-12">
        <div class="m-alert m-alert--icon alert alert-danger eth-errors" role="alert" style="display:none">
            <div class="m-alert__icon"><i class="flaticon-danger"></i></div>
            <div class="m-alert__text"></div>
        </div>

        <?=$this->render('_credit')?>

        <!--begin:: Widgets/Support Tickets -->
        <div class="m-portlet m-portlet--full-height  ">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            <?=Yii::t('app', 'Created Tasks') ?>
                        </h3>
                    </div>
                </div>
                <div class="m-portlet__head-tools">
                    <ul class="m-portlet__nav">
                        <li class="m-portlet__nav-item">
                            <a href="<?=Url::to(['task/new']) ?>" class="btn btn-info m-btn m-btn--icon">
          						<span>
          							<i class="la la-plus"></i>
          							<span>
          								<?=Yii::t('app', 'Create New Task') ?>
          							</span>
          						</span>
                            </a>
                        </li>
                        <!--li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                            <a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
                                <i class="la la-ellipsis-h m--font-brand"></i>
                            </a>
                            <div class="m-dropdown__wrapper">
                                <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                <div class="m-dropdown__inner">
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="m-nav">
                                                <li class="m-nav__item">
                                                    <a href="" class="m-nav__link">
                                                        <i class="m-nav__link-icon flaticon-share"></i>
                                                        <span class="m-nav__link-text">
                                                            Show deleted
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li-->
                    </ul>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="m-widget4 m-widget4--progress">
                    <?php
                    foreach ($tasks as $task) {
                        echo $this->render('_task', [
                            'task' => $task,
                            'view' => new TaskDetailView($task),
                        ]);
                    }

                    if (empty($tasks)):
                        ?>
                        <div class="lead">
                            <?=Yii::t('app', 'There is no created tasks. Please create the new one.'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!--end:: Widgets/Support Tickets -->
    </div>
</div>

<div class="modal fade" id="delete_task_modal" tabindex="-1" role="dialog" aria-labelledby="delete_task_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Delete task
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
                <button type="button" class="btn btn-secondary break-delete-link" data-dismiss="modal">
                    No
                </button>
                <a href="<?=Url::toRoute('task/delete')?>" class="btn btn-danger confirm-delete-link">
                    <i class="fa fa-trash-o"></i>
                    Yes
                </a>
            </div>
        </div>
    </div>
</div>