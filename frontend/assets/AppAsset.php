<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $css = [
        'site.css',
    ];
    public $js = [
        '//ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js',
        'webfont-init.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
