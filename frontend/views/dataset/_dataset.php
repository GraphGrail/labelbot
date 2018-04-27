<?php
/* @var $this yii\web\View */
/* @var $dataset \common\models\Dataset */
$formatter = \Yii::$app->formatter;
$status = $dataset->status();
?>
<div class="m-widget4__item dataset-item" data-id="<?=$dataset->id?>" data-delete-url="<?=\yii\helpers\Url::toRoute(sprintf('dataset/%s/delete', $dataset->id))?>" >
    <div class="m-widget4__img m-widget4__img--pic">
        <div class="m-demo-icon" style="margin-bottom: 0px;">
            <div class="m-demo-icon__preview">
                <i class="flaticon-tabs"></i>
            </div>
        </div>
    </div>
    <div class="m-widget4__info">
        <span class="m-widget4__title">
            <?=$dataset->name ?>
        </span>
        <br>
        <span class="m-widget4__sub">
            <?=Yii::t('app', 'Uploded at') ?> <?=$formatter->asDatetime($dataset->created_at, 'long') ?>
        </span>
    </div>
    <div class="m-widget4__progress">
        <?=$dataset->description ?>
    </div>
    <div class="m-widget4__ext">
        <span class="m--font-<?=$status->color ?> <?=isset($status->reload) ? 'js-reload' : null ?>">
			<?=$status->text ?>
		</span>
    </div>
    <div class="m-widget4__ext">
        <ul class="m-portlet__nav" style="margin-bottom: 0">
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
                                        <a href="javascript:void(0)" class="m-nav__link dataset-delete-link" data-id="<?=$dataset->id?>" data-toggle="modal" data-target="#delete_dataset_modal">
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
