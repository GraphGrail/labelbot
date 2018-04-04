<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

use frontend\assets\pages\TaskDetailPage;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task \common\models\Task */
/* @var $view \common\models\view\TaskDetailView */
/* @var $contractStatus object */
/* @var $assignedCount int */

TaskDetailPage::register($this);

?>

<input type="hidden" class="form-control m-input js-workers-source" disabled="disabled" value="<?=$view->getTableSourceAsJson()?>" />
<div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    <?=Yii::t('app', "Detail data for \"{$view->getName()}\" task.") ?>
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
                    <div>
                        <a href="<?= Url::toRoute(['task/score-work', 'id' => $task->id])?>" class="btn btn-accent">Score work</a>
                    </div>
                    <div class="m--margin-bottom-10"></div>
                    Complete percent: <strong><?=$view->getCompletedPercent()?></strong>
                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                    <p class="m--font-metal">
                        completed/all <?=$view->getApprovedCount()?>/<?=$view->getFullCount()?>
                    </p>
                </div>
            </div>
        </div>
        <!--end: Search Form -->
        <!--begin: Datatable -->
        <div class="m_datatable" id="ajax_data"></div>
        <!--end: Datatable -->
    </div>
</div>
