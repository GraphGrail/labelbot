<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Moderator */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Moderators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moderator-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('adm', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('adm', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('adm', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'auth_token',
            'eth_addr',
            'tg_chat_id',
            'tg_id',
            'tg_username',
            'tg_first_name',
            'tg_last_name',
            'phone',
            'current_task',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
