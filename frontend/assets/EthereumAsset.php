<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class EthereumAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $js = [
        'eth-gateway.js',
        'eth-main.js',
    //    'eth-gateway.js.map',
    ];

    public $depends = [
        InitAsset::class,
    ];
}