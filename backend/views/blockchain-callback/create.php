<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BlockchainCallback */

$this->title = Yii::t('adm', 'Create Blockchain Callback');
$this->params['breadcrumbs'][] = ['label' => Yii::t('adm', 'Blockchain Callbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blockchain-callback-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
