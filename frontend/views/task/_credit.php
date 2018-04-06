<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

use yii\helpers\Html;
use yii\helpers\Url;


/** @var \common\models\Task $task */

?>

<div class="m-alert m-alert--icon alert alert-danger js-credit-invitation" role="alert" style="display:none">
    <div class="m-alert__icon">
        <i class="flaticon-danger"></i>
    </div>
    <div class="m-alert__text">
        <?=Yii::t('app', 'You haven\'t enough ethereum or tokens in your wallet. Please, get free credit.') ?>
    </div>
    <div class="m-alert__actions credit-action" style="width: 220px;">
        <?=Html::tag('a', 'Get credit', [
            'class' => 'btn btn-link btn-outline-light btn-sm m-btn m-btn--hover-secondary js-get-credit',
            'href' => isset($task) ? Url::toRoute(['task/get-credit', 'id' => $task->id, 'address' => $task->contract_address]) : 'javascript:void(0);',
        ]) ?>
    </div>
</div>
