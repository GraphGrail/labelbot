<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Exception\TelegramException;
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
     * @return \Longman\TelegramBot\Entities\ServerResponse
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
            default:
                throw new HttpException(400, 'Error checking callback_data type');
        }
    }
}
