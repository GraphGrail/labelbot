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
use common\models\AssignedLabel;
use common\components\KeyboardGenerator;


/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class LabelKeyCallbackCommand extends AuthenticatedUserCommand
{
    /**
     * @var string
     */
    protected $name = 'labelkeycallback';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $usage = '/labelkeycallback';

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

        $next_labels = Label::findAll(['parent_label_id' => $label_id]);
        if ($next_labels) {
            $nextLabelGroup = Label::findOne($label_id);

            $inline_keyboard = KeyboardGenerator::labelsKeyboard($nextLabelGroup, $data_id, $this->moderator);

            $req_data = [
                'chat_id'      => $this->chat_id,
                'message_id'   => $this->message_id,
                'text'         => $this->message->getText(),
                'reply_markup' => $inline_keyboard,
            ];

            return Request::editMessageText($req_data);
        }

        $earlyAssignedLabel = AssignedLabel::findOne([
            'data_id'      => $data_id,
            'moderator_id' => $this->moderator->id,                    
        ]);

        if ($earlyAssignedLabel) {
            $req_data = [
                'callback_query_id' => $this->callback_query_id,
                'text'              => 'This data was labeled already',
                'show_alert'        => false,
                'cache_time'        => 0,
            ];
            Request::answerCallbackQuery($req_data);
            return $this->telegram->executeCommand('getdata');
        }

        $assignedLabel = new AssignedLabel;
        $assignedLabel->data_id      = $data_id;
        $assignedLabel->label_id     = $label_id;
        $assignedLabel->moderator_id = $this->moderator->id;
        $assignedLabel->created_at   = time();

        if (!$assignedLabel->save()) {
            // TODO: log error
        }

        return $this->telegram->executeCommand('getdata');
    }
}
