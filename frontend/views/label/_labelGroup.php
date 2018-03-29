<?php
/* @var $this yii\web\View */
/* @var $labelGroup \common\models\LabelGroup */

$formatter = \Yii::$app->formatter;
?>
<div class="m-widget4__item label-item" data-id="<?=$labelGroup->id?>" data-delete-url="<?=\yii\helpers\Url::toRoute(sprintf('label/%s/delete', $labelGroup->id))?>" >
    <div class="m-widget4__img m-widget4__img--pic">
    </div>
    <div class="m-widget4__info">
        <span class="m-widget4__title">
            <?=$labelGroup->name ?>
        </span>
        <br>
        <span class="m-widget4__sub">
            <?=Yii::t('app', 'Created at') ?> <?=$formatter->asDatetime($labelGroup->created_at, 'long') ?>
            <br>
            <?=$labelGroup->description ?>
        </span>
    </div>
    <div class="m-widget4__progress">
    </div>
    <div class="m-widget4__ext">
        <ul class="m-portlet__nav">
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
                                        <a href="javascript:void(0)" class="m-nav__link label-delete-link" data-id="<?=$labelGroup->id?>" data-toggle="modal" data-target="#delete_label_modal">
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
</div>
