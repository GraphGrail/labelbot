<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class DashBoardAsset extends AssetBundle
{
    public $sourcePath = '@resources/dashboard';

    public $js = [
        'dashboard.js',
    ];

    public $depends = [
        InitAsset::class,
    ];

}