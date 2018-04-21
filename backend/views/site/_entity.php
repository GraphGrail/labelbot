<?php
/**
 * Created by PhpStorm.
 * User: bytecrow
 * Date: 20.04.2018
 * Time: 13:31
 */

use yii\helpers\BaseInflector;

?>

<div class="col-lg-4">
    <h2><?=$name ?></h2>
    <p><?=$description ?></p>
    <p><a class="btn btn-default" href="<?=BaseInflector::camel2id($name) ?>s">View <?=$name ?>s</a></p>
</div>