<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;
use yii\web\View;

class VendorsAsset extends AssetBundle
{
    public $sourcePath = '@resources/vendors';

    public $css = [
        'vendors.bundle.css',
    ];
    public $js = [
        'vendors.bundle.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];

    public $depends = [
        InitAsset::class,
    ];
}