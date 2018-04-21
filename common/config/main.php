<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'bootstrap' => [
        'queue', // The component registers own console commands
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => getenv('DB_CHARSET') ?: 'utf8',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => getenv('SMTP_HOST'),
                'username' => getenv('SMTP_USERNAME'),
                'password' => getenv('SMTP_PASSWORD'),
                'port' => getenv('SMTP_PORT') ?: '587',
                'encryption' => getenv('SMTP_ENCRYPT') ?: 'tls',
            ],
        ],
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
                'adm*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'adm.php',
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
            'basePath' => '@fileStorage',
            'dirPermission' => 0777, //менять только если очередь работает от того же пользователя что и фронт
            'filePermission' => 0777,
            'buckets' => [
                'result' => [
                    'baseSubPath' => 'result',
                    'fileSubDirTemplate' => '{^name}/{^^name}',
                ],
                'datasets' => [
                    'baseSubPath' => 'datasets',
                    'fileSubDirTemplate' => '{^name}/{^^name}',
                ],
            ]
        ],
    ],
];
