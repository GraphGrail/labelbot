<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    'telegram_bot_api_key'  			=> '',
    'telegram_bot_username' 			=> '',
    'telegram_bot_webhook_token'		=> '',
    'telegram_bot_callback_secret_key'  => '',
    'telegram_bot_appmetrica_key'       => '',
    // IDs of admin users (leave as empty array if not used)
    'telegram_bot_admin_users' => [],
    // MySQL database credentials
	'telegram_bot_mysql_credentials' => [
        'host'     => 'localhost',
        'user'     => '',
        'password' => '',
        'database' => '',
    ],
];
