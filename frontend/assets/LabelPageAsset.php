<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class LabelPageAsset extends AssetBundle
{

    public $sourcePath = '@resources/pages';

    public $js = [
        'label.js'
    ];

    public $depends = [
    ];
}