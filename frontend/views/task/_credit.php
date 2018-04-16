<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

use yii\helpers\Html;

/** @var \common\models\Task $task */

?>

<div class="m-alert m-alert--icon alert alert-danger js-credit-invitation" role="alert" style="display:none">
    <div class="m-alert__icon">
        <i class="flaticon-danger"></i>
    </div>
    <div class="m-alert__text js-credit-text">
        <?=Yii::t('app', 'You haven\'t enough ethereum or tokens in your wallet. Please, get free credit.') ?>
    </div>
    <div class="m-alert__actions credit-action" style="width: 220px;">
        <?=Html::tag('button', 'Get credit', [
            'class' => 'btn btn-link btn-outline-light btn-sm m-btn m-btn--hover-secondary js-get-credit',
            'href' => '#',
        ]) ?>
    </div>
</div>

<div class="m-alert m-alert--icon alert alert-info js-credit-waiting" role="alert" style="display:none">
    <div class="m-alert__icon">
        <i class="flaticon-stopwatch"></i>
    </div>
    <div class="m-alert__text js-credit-text">
        <?=Yii::t('app', 'Waiting for the credit. It may take a minute or two.') ?>
    </div>
</div>
