<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $css = [
        'style.bundle.css',
    ];
    public $js = [
        'scripts.bundle.js',
    ];

    public $depends = [
        InitAsset::class,
        VendorsAsset::class,
        FullCalendarAsset::class,
        DashBoardAsset::class,
    ];
}