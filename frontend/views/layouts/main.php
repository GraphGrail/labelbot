<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use frontend\assets\EthereumAsset;
use yii\helpers\Html;

AppAsset::register($this);
EthereumAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->head() ?>
    <link rel="shortcut icon" href="/images/logo/favicon.ico" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
</head>
<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
<?php $this->beginBody() ?>
<div class="js-token-contract-address" style="display: none"><?=Yii::$app->params['tokenContractAddress'] ?></div>
<div class="js-eth-network-id" style="display: none"><?=Yii::$app->params['networkId'] ?></div>

    <!-- begin:: Page -->
    <div class="m-grid m-grid--hor m-grid--root m-page">
        <?=$this->render('_header');?>
        <!-- begin::Body -->
        <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
            <?=$this->render('_left_menu')?>
            <div class="m-grid__item m-grid__item--fluid m-wrapper">
                <!-- BEGIN: Subheader -->
                <div class="m-subheader ">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h1 class="m-subheader__title">
                                <?= Html::encode($this->title) ?>
                            </h1>
                        </div>
                    </div>
                </div>
                <!-- END: Subheader -->
                <div class="m-content">
                    <?= $content ?>
                </div>
            </div>
        </div>
        <!-- end:: Body -->
        <?=$this->render('_footer')?>
    </div>
    <!-- end:: Page -->
    <?=$this->render('_sidebar')?>
    <!-- begin::Scroll Top -->
    <div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500" data-scroll-speed="300">
        <i class="la la-arrow-up"></i>
    </div>
    <!-- end::Scroll Top -->             

    <!-- begin::Quick Nav -->
    <!--ul class="m-nav-sticky" style="margin-top: 30px;">
        <li class="m-nav-sticky__item" data-toggle="m-tooltip" title="Showcase" data-placement="left">
            <a href="">
                <i class="la la-eye"></i>
            </a>
        </li>
        <li class="m-nav-sticky__item" data-toggle="m-tooltip" title="Pre-sale Chat" data-placement="left">
            <a href="" >
                <i class="la la-comments-o"></i>
            </a>
        </li>
        <li class="m-nav-sticky__item" data-toggle="m-tooltip" title="Purchase" data-placement="left">
            <a href="/" target="_blank">
                <i class="la la-cart-arrow-down"></i>
            </a>
        </li>
        <li class="m-nav-sticky__item" data-toggle="m-tooltip" title="Documentation" data-placement="left">
            <a href="/" target="_blank">
                <i class="la la-code-fork"></i>
            </a>
        </li>
        <li class="m-nav-sticky__item" data-toggle="m-tooltip" title="Support" data-placement="left">
            <a href="/" target="_blank">
                <i class="la la-life-ring"></i>
            </a>
        </li>
    </ul-->
    <!-- begin::Quick Nav -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
