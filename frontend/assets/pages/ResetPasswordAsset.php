<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets\pages;


use frontend\assets\AppAsset;
use yii\web\AssetBundle;

class ResetPasswordAsset extends AssetBundle
{
    public $sourcePath = '@resources/pages';

    public $js = [
        'resetPassword.js',
    ];

    public $depends = [
        AppAsset::class,
    ];

}