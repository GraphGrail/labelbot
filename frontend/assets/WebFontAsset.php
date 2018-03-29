<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;
use yii\web\View;

class WebFontAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $js = [
        '//ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js',
        'webfont-init.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}