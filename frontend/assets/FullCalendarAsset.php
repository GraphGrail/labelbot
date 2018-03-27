<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class FullCalendarAsset extends AssetBundle
{
    public $sourcePath = '@resources/fullcalendar';

    public $css = [
        'fullcalendar.bundle.css',
    ];
    public $js = [
        'fullcalendar.bundle.js',
    ];

    public $depends = [
        AppAsset::class,
    ];

}