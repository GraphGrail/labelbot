<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    /**
     * Telegram
     */
    'telegram_bot_api_key'  			=> '',
    'telegram_bot_username' 			=> '',
    'telegram_bot_webhook_token'		=> md5('SeCrEt_T0k3n'),
    'telegram_bot_callback_secret_key'  => '',
    'telegram_bot_appmetrica_key'       => '',
    // IDs of admin users (leave as empty array if not used)
    'telegram_bot_admin_users' => [],
    // Telegram MySQL database credentials
	'telegram_bot_mysql_credentials' => [
        'host'     => 'localhost',
        'user'     => '',
        'password' => '',
        'database' => '',
    ],

    /**
     * Datasets Uploadings
     */
    'datasetsUploadDir'     => '@frontend/runtime/uploads/datasets/',
    'datasetFileMaxSize'    => 40*1024*1024,
    'datasetFileMaxRecords' => 2000,
    'datasetDataMaxSize'    => 50*1024,

    /**
     * Ethereum Gateway
     */
    'eth_gateway_url'            => 'http://127.0.0.1:3000/api/',
    'eth_gateway_callback_url'   => 'http://tgbot.test/blockchain-callback/',
    'eth_gateway_callback_token' => md5('SeCrEt_T0k3n_2'),
];
