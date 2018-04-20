<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ModeratorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('adm', 'Moderators');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moderator-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('adm', 'Create Moderator'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'auth_token',
            'eth_addr',
            'tg_chat_id',
            'tg_id',
            //'tg_username',
            //'tg_first_name',
            //'tg_last_name',
            //'phone',
            //'current_task',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
