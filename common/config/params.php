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
    'ethGatewayCallbackUrl' => 'http://tgbot.test/blockchain-callback/',
    'ethGatewayApiUrl'      => 'http://127.0.0.1:3000/api/',
    'networkId'             => 1337,

    /**
     * Blockchain configs
     */
    'tokenContractAddress'                    => '0x436e362ac2c1d5f88986b7553395746446922be2',
    // Contract defaults
    'workItemSize'                            => '20',
    'workItemPrice'                           => '1000000000000000',
    'approvalCommissionBenificiaryAddress'    => '0x24a8dcf36178e239134ce89f74b45d734b5780f8',
    'disapprovalCommissionBeneficiaryAddress' => '0xe354a075b40ce98f1e1b377c0420020f358f2e48',
    'approvalCommissionFraction'              => 0.1,
    'disapprovalCommissionFraction'           => 0.2,
    'autoApprovalTimeoutSec'                  => 60,
    // User credit defaults
    'creditEtherValue' => '1000000000000000000',
    'creditTokenValue' => '1000000000000000000',

];
