<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $css = [
        'site.css',
    ];

    public $depends = [
        YiiAsset::class,
        WebFontAsset::class,
    ];
}
