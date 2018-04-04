<?php
/* @var $this yii\web\View */

use common\models\Task;
use common\models\view\TaskDetailView;
use yii\helpers\Url;

/* @var $task Task */
/* @var $view TaskDetailView */

$formatter = \Yii::$app->formatter;

$detailUrl = Url::toRoute([$task->status < Task::STATUS_CONTRACT_ACTIVE ? 'task/smart-contract' : 'task/view', 'id' => $task->id]);

?>
<div class="m-widget4__item task-item" data-id="<?=$task->id?>" data-delete-url="<?= Url::toRoute(sprintf('task/%s/delete', $task->id))?>" >
    <div class="m-widget4__img m-widget4__img--pic">
        <div class="m-demo-icon" style="margin-bottom: 0px;">
            <div class="m-demo-icon__preview">
                <i class="flaticon-notes"></i>
            </div>
        </div>
    </div>
    <div class="m-widget4__info">
        <span class="m-widget4__title">
            <a href="<?=$detailUrl?>" style="color: #575962"><?=$task->name ?></a>
        </span>
        <br>
        <span class="m-widget4__sub">
            <?=Yii::t('app', 'Created at') ?> <?=$formatter->asDatetime($task->created_at, 'long') ?>
            <br>
            <?=$task->description ?>
        </span>
    </div>
    <div class="m-widget4__progress">
        <div class="m--font-<?=$view->getStatusColor()?>">
			<?=$view->getStatusLabel()?>
        </div>
        <?php
        if ($dataset = $task->getDataset()) {
            printf('%s: %s<br>', $task->getAttributeLabel('dataset_id'), $dataset);
        }
        if ($labelGroup = $task->getLabelGroup()) {
            printf('%s: %s<br>', $task->getAttributeLabel('label_group_id'), $labelGroup);
        }
        ?>
    </div>
    <div class="m-widget4__ext">
        <span style="overflow: visible; display: block; width: 179px; text-align: right">
            <?php
                if ($action = $view->getNextAction()) {
                    ?>
                    <a href="<?=$action->getUrl() ?: 'javascript:void(0);'?>" class="<?=$action->getOptions()['class']?>  js-btn-release" style="margin-right: 10px;">
                        <?=$action->getLabel()?>
                    </a>
                    <?php
                } else {
                    ?>
                    <div class="dropdown ">
                        <ul class="m-portlet__nav" style="display: inline-block;">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="javascript:void(0)" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
                                    <i class="la la-ellipsis-h m--font-brand"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 22.5px;"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">
                                                    <li class="m-nav__item">
                                                        <a href="<?=$detailUrl?>" class="m-nav__link">
                                                            <i class="m-nav__link-icon fa fa-pencil-square-o"></i>
                                                            <span class="m-nav__link-text">
                                                                Detail
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0)" class="m-nav__link task-delete-link" data-id="<?=$task->id?>" data-toggle="modal" data-target="#delete_task_modal">
                                                            <i class="m-nav__link-icon fa fa-trash-o m--font-danger"></i>
                                                            <span class="m-nav__link-text m--font-danger">
                                                                Delete
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                <?php
                }
            ?>
        </span>

    </div>
</div>
