<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => [
        'queue', // The component registers own console commands
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'tg*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'tg.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'path' => '@console/runtime/queue',
        ],
        'fileStorage' => [
            'class' => \yii2tech\filestorage\local\Storage::class,
            'basePath' => '@resources/files',
            'dirPermission' => 0775,
            'filePermission' => 0755,
            'buckets' => [
                'result' => [
                    'baseSubPath' => 'result',
                    'fileSubDirTemplate' => '{^name}/{^^name}',
                ],
            ]
        ],
    ],
];
