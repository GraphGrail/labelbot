<?php

/* @var $this \yii\web\View */
$isAuth = (bool)Yii::$app->user->identity;
?>
<!-- BEGIN: Header -->
<header class="m-grid__item    m-header "  data-minimize-offset="200" data-minimize-mobile-offset="200" >
    <div class="m-container m-container--fluid m-container--full-height">
        <div class="m-stack m-stack--ver m-stack--desktop">
            <?=$this->render(($isAuth ? 'auth/' : 'unauth/') . '_left_menu_header_part')?>
            <?=$this->render(($isAuth ? 'auth/' : 'unauth/') . '_header_main_panel_part')?>
        </div>
    </div>
</header>
<!-- END: Header -->