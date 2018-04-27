<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

use common\models\Task;

/* @var $this yii\web\View */
/* @var $task Task */

$this->registerJs('
    setTimeout(function(){ window.location.reload(); }, 5000);
');

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
                                Score work for task "<?=$task->name ?>"
                            </h5>
                            <div class="m-loader m-loader--success">Pausing task...</div>
                            <div class="m--padding-bottom-20"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
