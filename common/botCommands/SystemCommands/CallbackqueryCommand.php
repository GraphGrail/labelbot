<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use common\components\CallbackData;
use yii\web\HttpException;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * Command execute method
     *
     * @return void
     * @throws HttpException
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_data_type = CallbackData::getType($this->getCallbackQuery()->getData());

        switch ($callback_data_type) {
            case CallbackData::LABEL_KEY_PRESSED:
                $this->telegram->executeCommand('labelkeycallback');
                break;
            case CallbackData::BACK_KEY_PRESSED:
                $this->telegram->executeCommand('backkeycallback');
                break;
            case CallbackData::NEXT_KEY_PRESSED:
                $this->telegram->executeCommand('nextkeycallback');
                break;
            default:
                throw new HttpException(400, 'Error checking callback_data type');
        }
    }
}
