<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets\pages;


use yii\web\AssetBundle;

class DatasetPageAsset extends AssetBundle
{

    public $sourcePath = '@resources/pages';

    public $js = [
        'dataset.js'
    ];

    public $depends = [
    ];
}