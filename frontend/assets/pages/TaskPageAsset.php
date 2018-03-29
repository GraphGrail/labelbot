<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets\pages;


use yii\web\AssetBundle;

class TaskPageAsset extends AssetBundle
{

    public $sourcePath = '@resources/pages';

    public $js = [
        'task.js'
    ];

    public $depends = [
    ];
}