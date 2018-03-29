<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class LoginAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $js = [
        'login.js',
    ];

    public $depends = [
        AppAsset::class,
    ];

}