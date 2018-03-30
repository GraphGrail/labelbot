<?php

use frontend\assets\pages\TaskPageAsset;
use yii\helpers\Url;

TaskPageAsset::register($this);

/* @var $this yii\web\View */
/* @var $tasks \common\models\Task[] */

$this->title = 'Tasks';
?>
<div class="row">
    <div class="col-xl-8">
        <!--begin:: Widgets/Support Tickets -->
        <div class="m-portlet m-portlet--full-height ">
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
                            'task' => $task
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