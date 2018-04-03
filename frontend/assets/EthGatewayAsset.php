<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class EthGatewayAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $js = [
        'eth-gateway.js',
        'eth-errors.js',
    //    'eth-gateway.js.map',
    ];

    public $depends = [
        InitAsset::class,
    ];
}