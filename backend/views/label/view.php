<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Label */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Labels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="label-view">

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
            'parent_label_id',
            'text',
            'label_group_id',
            'ordering',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
