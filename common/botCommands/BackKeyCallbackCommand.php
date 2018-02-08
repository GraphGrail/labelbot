<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\AuthenticatedUserCommand;
use Longman\TelegramBot\Request;
use common\components\CallbackData;
use common\models\Label;
use common\components\LabelsKeyboard;


/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class BackKeyCallbackCommand extends AuthenticatedUserCommand
{
    /**
     * @var string
     */
    protected $name = 'backkeycallback';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $usage = '/backkeycallback';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_data = new CallbackData($this->moderator, $this->callback_query_data);
        $verified_callback_data = $callback_data->getVerifiedData();
        list($data_id, $label_id) = explode(':', $verified_callback_data);

        $root_label = Label::findOne($label_id);
        $inline_keyboard = new LabelsKeyboard($root_label, $data_id, $this->moderator);
        $req_data = [
            'chat_id'      => $this->chat_id,
            'message_id'   => $this->message_id,
            'text'         => $this->message->getText(),
            'reply_markup' => $inline_keyboard->generate(),
        ];
        return Request::editMessageText($req_data);
    }
}
