<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

/* @var $this \yii\web\View */
$isAuth = (bool)Yii::$app->user->identity;

echo $this->render(($isAuth ? 'auth/' : 'unauth/') . '_left_menu_items_part');