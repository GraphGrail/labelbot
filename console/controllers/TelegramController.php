<?php

namespace console\controllers;

use Yii;
use yii\base\Application;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;
use yii\helpers\Inflector;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramController extends \yii\console\Controller
{
    protected $telegram_api_url;

    public function __construct(...$params)
    {
        $this->telegram_api_url = 'https://api.telegram.org/bot' 
                          . Yii::$app->params['telegram_bot_api_key'] . '/';

        parent::__construct(...$params);
    }

    public function actionSetWebhook($domain = null, $certificate = null)
    {
        if ($domain === null) {
            echo $this->ansiFormat('Error: ', Console::FG_RED) 
                . 'You need to specify <domain> param' . PHP_EOL
                . 'You can use ngrok (https://ngrok.com) as SSL proxy:' . PHP_EOL
                . 'ngrok http -host-header=<local_domain> 80';
            return Controller::EXIT_CODE_ERROR;
        }

        $url = $domain . '/webhook/' . Yii::$app->params['telegram_bot_webhook_token'];
        $params = [];

        if ($certificate) {
            $params['certificate'] = $certificate;
        }

        try {
            $telegram = new Telegram(
                    Yii::$app->params['telegram_bot_api_key'], 
                    Yii::$app->params['telegram_bot_username']
                );

            $result = $telegram->setWebhook($url, $params);
            if ($result->isOk()) {
                echo $result->getDescription();
            }
        } catch (TelegramException $e) {
            echo $e->getMessage();
        }

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionRemoveWebhook()
    {
        try {
            $telegram = new Telegram(
                    Yii::$app->params['telegram_bot_api_key'], 
                    Yii::$app->params['telegram_bot_username']
                );
            $result = $telegram->deleteWebhook();
            if ($result->isOk()) {
                echo $result->getDescription();
            }
        } catch (TelegramException $e) {
            echo $e->getMessage();
        }

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionGetWebhookInfo()
    {
        echo file_get_contents($this->telegram_api_url . 'getWebhookInfo');
        return Controller::EXIT_CODE_NORMAL;
    }
}
