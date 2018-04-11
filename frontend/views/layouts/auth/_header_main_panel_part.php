<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\EthGatewayAsset;

EthGatewayAsset::register($this);

$this->registerJs("

const userWallet = function () {
    const bigNum = graphGrailEther.BigNumber
    const tokenContractAddress = '" . Yii::$app->params['tokenContractAddress'] . "'
    const expectedNetworkId = '" . Yii::$app->params['networkId'] . "'
    let clientAddress

    function initWallet() {
        graphGrailEther.init(tokenContractAddress, expectedNetworkId)
            .catch(err => null)
        return !graphGrailEther.isInitialized() ?
          new Promise(resolve => setTimeout(initWallet, 50)) :
          showWallet()
    };

    initWallet();

    function showWallet() {
      graphGrailEther.getClientAddress()
        .then(address => {
            if (!address) {
                return
            }
            console.log('User wallet address: ' + address)
            clientAddress = address
            $('.js-address').val(address)
            return graphGrailEther.checkBalances(address)
        })
        .then(balances => {
            if (!balances) {
                return;
            }
            console.log('Ether: ' + balances.ether + ', tokens: ' + balances.token)
            $('.js-user-addr').text(clientAddress)
            $('.js-user-ether').text(new bigNum(balances.ether).dividedBy('1e18').toFormat(6))
            $('.js-user-token').text(new bigNum(balances.token).dividedBy('1e18').toFormat(6))
            $('.js-user-wallet').removeClass('m--hide')
        })
        .catch(err => {
            console.log(err.code + ' ' + err)
        })
    };
}();


");

?>
<div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
    <!-- BEGIN: Horizontal Menu -->
    <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-dark "
            id="m_aside_header_menu_mobile_close_btn">
        <i class="la la-close"></i>
    </button>
    <div id="m_header_menu"
         class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
        <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
            <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel" data-menu-submenu-toggle="click"
                data-redirect="true" aria-haspopup="true">
                <a href="#" class="m-menu__link m-menu__toggle">
                    <i class="m-menu__link-icon flaticon-add"></i>
                    <span class="m-menu__link-text">
                        <?= Yii::t('app', 'Actions') ?>
                    </span>
                    <i class="m-menu__hor-arrow la la-angle-down"></i>
                    <i class="m-menu__ver-arrow la la-angle-right"></i>
                </a>
                <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                    <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                    <ul class="m-menu__subnav">
                        <li class="m-menu__item " aria-haspopup="true">
                            <a href="<?= Url::to(['dataset/new']) ?>" class="m-menu__link ">
                                <i class="m-menu__link-icon flaticon-file"></i>
                                <span class="m-menu__link-text">
                                    <?= Yii::t('app', 'Add New Dataset') ?>
                                </span>
                            </a>
                        </li>
                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                            <a href="<?= Url::to(['label/new']) ?>" class="m-menu__link ">
                                <i class="m-menu__link-icon flaticon-diagram"></i>
                                <span class="m-menu__link-title">
                                    <span class="m-menu__link-wrap">
                                        <span class="m-menu__link-text">
                                            <?= Yii::t('app', 'Create New Labeling') ?>
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                            <a href="<?= Url::to(['task/new']) ?>" class="m-menu__link ">
                                <i class="m-menu__link-icon flaticon-notes"></i>
                                <span class="m-menu__link-title">
                                    <span class="m-menu__link-wrap">
                                        <span class="m-menu__link-text">
                                            <?= Yii::t('app', 'Create New Task') ?>
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <!-- END: Horizontal Menu -->
    <!-- BEGIN: Topbar -->
    <div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general">
        <div class="m-stack__item m-topbar__nav-wrapper">
            <ul class="m-topbar__nav m-nav m-nav--inline">

                <li class="m-nav__item m-dropdown m-dropdown--large m-dropdown--arrow m-dropdown--align-center m-dropdown--mobile-full-width m-list-search m-list-search--skin-light">
                    <a class="m-nav__link m--hide m--font-brand js-user-wallet">
                        Wallet: <span class="m--font-focus js-user-addr"></span><br>
                        Ether: <span class="js-user-ether"></span>, GAI tokens: <span class="js-user-token"></span><br>
                    </a>
                </li>

                <li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
                    data-dropdown-toggle="click">
                    <a href="#" class="m-nav__link m-dropdown__toggle m-demo-icon">
                        <span class="m-topbar__userpic m-demo-icon m-demo-icon__preview">
                            <i class="m--img-rounded m--marginless m--img-centered flaticon-profile-1"></i>
                        </span>
                        <span class="m-topbar__username m--font-brand m--hide">
                            <?= Yii::$app->user->identity->username; ?>
                        </span>
                    </a>
                    <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                        <div class="m-dropdown__inner">
                            <div class="m-dropdown__header m--align-center"
                                 style="background: url(/images/misc/user_profile_bg.jpg); background-size: cover;">
                                <div class="m-card-user m-card-user--skin-dark">
                                    <div class="m-card-user__pic">
<!--                                        <img src="/images/users/user4.jpg" class="m--img-rounded m--marginless" alt=""/>-->
                                    </div>
                                    <div class="m-card-user__details">
                                        <span class="m-card-user__name m--font-weight-500">
                                            <?= Yii::$app->user->identity->username; ?>
                                        </span>
                                        <a href="" class="m-card-user__email m--font-weight-300 m-link">
                                            <?= Yii::$app->user->identity->email; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="m-dropdown__body">
                                <div class="m-dropdown__content">
                                    <ul class="m-nav m-nav--skin-light">
                                        <li class="m-nav__section m--hide">
                                            <span class="m-nav__section-text">
                                                Section
                                            </span>
                                        </li>
                                        <!--li class="m-nav__item">
                                            <a href="/" class="m-nav__link">
                                                <i class="m-nav__link-icon flaticon-profile-1"></i>
                                                <span class="m-nav__link-title">
                                                    <span class="m-nav__link-wrap">
                                                        <span class="m-nav__link-text">
                                                            My Profile
                                                        </span>
                                                    </span>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="m-nav__separator m-nav__separator--fit"></li>
                                        <li class="m-nav__item">
                                            <a href="header/profile.html" class="m-nav__link">
                                                <i class="m-nav__link-icon flaticon-lifebuoy"></i>
                                                <span class="m-nav__link-text">
                                                    Support
                                                </span>
                                            </a>
                                        </li-->
                                        <li class="m-nav__separator m-nav__separator--fit"></li>
                                        <li class="m-nav__item">
                                            <?= Html::a('Logout', ['site/logout'], [
                                                'data' => ['method' => 'post'],
                                                'class' => 'btn m-btn--pill btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder'
                                            ]) ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li id="m_quick_sidebar_toggle" class="m-nav__item">
                    <a href="#" class="m-nav__link m-dropdown__toggle">
                                            <span class="m-nav__link-icon">
                                                <i class="flaticon-grid-menu"></i>
                                            </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- END: Topbar -->
</div>
