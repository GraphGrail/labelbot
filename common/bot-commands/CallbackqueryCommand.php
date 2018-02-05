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
use common\components\Bot;
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
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $bot = new Bot($this);
        $callback_data = new CallbackData($bot->moderator, $bot->callback_query_data);

        if (!$callback_data->checkSign()) {
            throw new HttpException(400, 'Error checking callback_data sign');
        }

        switch ($callback_data->type) {
            case CallbackData::LABEL_ASSIGN:
                $bot->assignLabel();
                break;
            case CallbackData::NEXT_LABEL_GROUP:
                $bot->nextLabelGroup();
                break;
            default:
                throw new HttpException(400, 'Error checking callback_data type');
        }

    }
}
