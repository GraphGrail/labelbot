<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets\pages;


use yii\web\AssetBundle;

class ScoreWorkAsset extends AssetBundle
{

    public $sourcePath = '@resources/pages';

    public $js = [
        'scoreWork.js'
    ];
}