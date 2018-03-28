<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class ResetPasswordAsset extends AssetBundle
{
    public $sourcePath = '@resources/main';

    public $js = [
        'resetPassword.js',
    ];

    public $depends = [
        AppAsset::class,
    ];

}