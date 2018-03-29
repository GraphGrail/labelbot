<?php

use yii\helpers\Html;
use frontend\assets\EthGatewayAsset;

/* @var $this yii\web\View */
EthGatewayAsset::register($this);

$this->registerJs("

");
?>
<h1>Smart-contract for task "<?=$task->name ?>"</h1>

<h1>Smart-contract on deployment</h1>
