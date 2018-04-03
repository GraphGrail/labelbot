<?php

use common\models\Task;
use frontend\assets\EthGatewayAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Task */
EthGatewayAsset::register($this);

$this->registerJs("
    setTimeout(function(){ window.location = '" . Url::toRoute(['task/view', 'id' => $task->id]) . "' }, 2000);
");
?>
<?php
if ($task->status >= Task::STATUS_CONTRACT_ACTIVE):
?>
    <div class="row">
        <div class="col-lg-8">
            <div class="m-portlet m-portlet--tab">
                <div class="m-portlet__body m-portlet__body--no-padding">
                    <div class="row m-row--no-padding m-row--col-separator-xl">
                        <div class="col-md-12">
                            <div class="m-widget1">
                                <div class="m--padding-bottom-5"></div>
                                <h5 class="m-widget5__title m--margin-bottom-25">
                                    Smart-contract for task "<?=$task->name ?>"
                                </h5>
                                <div class="m-loader m-loader--success">Smart-contract in activating process</div>
                                <div class="m--padding-bottom-20"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
endif;
