<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class ThemeJsAsset extends AssetBundle
{
    public $sourcePath = '@resources/custom';

    public $js = [
        'components/base/toastr.js'
    ];
}
