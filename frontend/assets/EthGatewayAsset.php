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
    ];

    public $depends = [
        InitAsset::class,
    ];
}