<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

use frontend\assets\EthGatewayAsset;
use frontend\assets\pages\TaskDetailPage;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task \common\models\Task */
/* @var $view \common\models\view\TaskDetailView */
/* @var $contractStatus object */
/* @var $assignedCount int */

TaskDetailPage::register($this);

?>
<div class="m-alert m-alert--icon alert alert-danger eth-errors" role="alert" style="display:none">
    <div class="m-alert__icon"><i class="flaticon-danger"></i></div>
    <div class="m-alert__text"></div>
</div>

<?=$this->render('_credit')?>

<input type="hidden" class="form-control m-input js-workers-source" disabled="disabled" value="<?=$view->getTableSourceAsJson()?>" />
<div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                <h3 class="m-portlet__head-text">
                    <?=Yii::t('app', "Detail data for \"{$view->getName()}\" task.") ?>
                    <span class="m--font-<?=$view->getStatusColor()?>">
                        <?=$view->getStatusLabel()?>
                    </span>
                </h3>
            </div>
        </div>
        <div class="m-portlet__head-tools"></div>
    </div>
    <div class="m-portlet__body">
        <!--begin: Search Form -->
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <?=$view->getStatusComment() ?>
            </div>
        </div>
        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
            <div class="row align-items-center">
                <div class="col-xl-8 order-2 order-xl-1">
                    <div class="form-group m-form__group row align-items-center">
                        <div class="col-md-8">
                            <?= \common\widgets\smartContractAddress::widget(['address'=>$task->contract_address]); ?>
                            <div class="d-md-none m--margin-bottom-10"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                    <div>
                        <?php
                            if ($action = $view->getNextAction()) {
                                ?>
                                    <a
                                        href="<?=$action->getUrl() ?: 'javascript:void(0);'?>"
                                        class="<?=$action->getOptions()['class']?>"
                                        data-id="<?=$task->id ?>"
                                        data-contract-address="<?=$task->contract_address ?>"
                                    ><?=$action->getLabel()?></a>
                                <?php
                            }
                            if ($additionalActions = $view->getAdditionalActions()) {
                                ?>
                                <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push m--margin-left-10" data-dropdown-toggle="hover" aria-expanded="true">
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
                                                                <a
                                                                        href="<?=$additionalAction->getUrl() ?: 'javascript: void(0);'?>"
                                                                        class="m-nav__link <?=$additionalAction->getOptions()['class']?>"
                                                                        data-id="<?=$task->id?>"
                                                                        data-contract-address="<?=$task->contract_address?>"
                                                                >
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
                    </div>
                    <div class="m--margin-bottom-10"></div>
                    Completeness percent: <strong><?=$view->getCompletedPercent()?></strong>
                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                    <p class="m--font-metal">
                        approved/all <?=$view->getApprovedCount()?>/<?=$view->getFullCount()?>
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
