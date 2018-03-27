<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $css = [
        'style.bundle.css',
    ];
    public $js = [
        'scripts.bundle.js',
    ];

    public $depends = [
        AppAsset::class,
        VendorsAsset::class,
        FullCalendarAsset::class,
        DashBoardAsset::class,
    ];
}