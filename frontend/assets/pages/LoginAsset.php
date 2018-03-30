<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets\pages;


use frontend\assets\AppAsset;
use yii\web\AssetBundle;

class LoginAsset extends AssetBundle
{
    public $sourcePath = '@resources/pages';

    public $js = [
        'login.js',
    ];

    public $depends = [
        AppAsset::class,
    ];

}