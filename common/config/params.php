<?php
return [
    'adminEmail' => getenv('ADMIN_EMAIL') ?: 'admin@example.com',
    'supportEmail' => getenv('SUPPORT_EMAIL') ?: 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    /**
     * Telegram
     */
    'telegram_bot_api_key'  			=> getenv('TG_BOT_API_KEY'),
    'telegram_bot_username' 			=> getenv('TG_BOT_USERNAME'),
    'telegram_bot_webhook_token'		=> getenv('TG_BOT_WEBHOOK_TOKEN') ?: md5('SeCrEt_T0k3n'),
    'telegram_bot_callback_secret_key'  => getenv('TG_BOT_CALLBACK_SECRET_KEY'),
    'telegram_bot_appmetrica_key'       => getenv('TG_BOT_APP_METRICA_KEY'),
    // IDs of admin users (leave as empty array if not used)
    'telegram_bot_admin_users' => [],
    // Telegram MySQL database credentials
	'telegram_bot_mysql_credentials' => [
        'host'     => getenv('TG_BOT_MYSQL_HOST') ?: 'localhost',
        'user'     => getenv('TG_BOT_MYSQL_USER'),
        'password' => getenv('TG_BOT_MYSQL_PASSWORD'),
        'database' => getenv('TG_BOT_MYSQL_DB'),
    ],

    /**
     * Datasets Uploadings
     */
    'datasetsUploadDir'     => 'datasets',
    'datasetFileMaxSize'    => 40*1024*1024,
    'datasetFileMaxRecords' => 2000,
    'datasetDataMaxSize'    => 50*1024,

    /**
     * Ethereum Gateway
     */
    'ethGatewayCallbackUrl' => getenv('ETH_CALLBACK_URL'),
    'ethGatewayApiUrl'      => getenv('ETH_API_URL') ?: 'http://127.0.0.1:3000/api/',
    'networkId'             => getenv('ETH_NETWORK_ID') ?: 1337,

    /**
     * Blockchain configs
     */
    'tokenContractAddress'                    => getenv('B_TOKEN_CONTRACT_ADDRESS') ?: '0x436e362ac2c1d5f88986b7553395746446922be2',
    // Contract defaults
    'workItemSize'                            => getenv('B_WORK_ITEM_SIZE') ?: '20',
    'workItemPrice'                           => getenv('B_WORK_ITEM_PRICE') ?: '1000000000000000',
    'approvalCommissionBenificiaryAddress'    => getenv('B_APPROVAL_COMMISSION_ADDR') ?: '0x24a8dcf36178e239134ce89f74b45d734b5780f8',
    'disapprovalCommissionBeneficiaryAddress' => getenv('B_DISAPPROVAL_COMMISSION_ADDR') ?: '0xe354a075b40ce98f1e1b377c0420020f358f2e48',
    'approvalCommissionFraction'              => 0.1,
    'disapprovalCommissionFraction'           => 0.2,
    'autoApprovalTimeoutSec'                  => 60,
    // User credit defaults
    'creditEtherValue' => getenv('CREDIT_ETH_VALUE') ?: '1000000000000000000',
    'creditTokenValue' => getenv('CREDIT_TOKEN_VALUE') ?: '1000000000000000000',

];
