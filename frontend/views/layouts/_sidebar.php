<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

if (Yii::$app->user->identity) {
    echo $this->render('auth/_sidebar');
}