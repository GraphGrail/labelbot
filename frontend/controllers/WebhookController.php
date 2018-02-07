<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Exception\TelegramLogException;


class WebhookController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

	/**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($token)
    {
        // Check webhook token
        if ($token !== Yii::$app->params['telegram_bot_webhook_token']) {
            throw new \yii\web\HttpException(403, 'Invalid token');
        }

        // Bot's configuration
        $bot_api_key  = Yii::$app->params['telegram_bot_api_key'];
        $bot_username = Yii::$app->params['telegram_bot_username'];
        $admin_users = Yii::$app->params['telegram_bot_admin_users'];
        // Paths for bot's commands
        $commands_paths = [
            Yii::getAlias('@common') . '/bot-commands/',
        ];
        $mysql_credentials = Yii::$app->params['telegram_bot_mysql_credentials'];

        try {
            $telegram = new Telegram($bot_api_key, $bot_username);
            $telegram->addCommandsPaths($commands_paths);
            $telegram->enableAdmins($admin_users);

            // Enable MySQL
            // $telegram->enableMySql($mysql_credentials);

            // Logging (Error, Debug and Raw Updates)
            TelegramLog::initErrorLog(Yii::getAlias('@runtime') . "/logs/{$bot_username}_error.log");
            TelegramLog::initDebugLog(Yii::getAlias('@runtime') . "/logs/{$bot_username}_debug.log");
            TelegramLog::initUpdateLog(Yii::getAlias('@runtime') . "/logs/{$bot_username}_update.log");

            // If you are using a custom Monolog instance for logging, use this instead of the above
            //Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);

            // Set custom Upload and Download paths
            //$telegram->setDownloadPath(__DIR__ . '/Download');
            //$telegram->setUploadPath(__DIR__ . '/Upload');

            // Here you can set some command specific parameters
            // e.g. Google geocode/timezone api key for /date command
            //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

            // Botan.io integration
            $telegram->enableBotan(Yii::$app->params['telegram_bot_appmetrica_key']);

            // Requests Limiter (tries to prevent reaching Telegram API limits)
            $telegram->enableLimiter();

            // Handle telegram webhook request
            $telegram->handle();
        } catch (TelegramException $e) {
            // echo $e;
            // Log telegram errors
            TelegramLog::error($e);
        } catch (TelegramLogException $e) {
            // Uncomment this to catch log initialisation errors
            echo $e;
        }
    }
}
