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
use common\components\labelsKeyboard;
use common\models\Data;
use common\models\Label;

/**
 * User "/getdata" command
 *
 * Display an inline keyboard with a few buttons.
 */
class GetdataCommand extends AuthenticatedUserCommand
{
    /**
     * @var string
     */
    protected $name = 'getdata';

    /**
     * @var string
     */
    protected $description = 'Get data for label assignment';

    /**
     * @var string
     */
    protected $usage = '/getdata';

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
        $data = Data::getForLabelAssignment(1, $this->moderator->id);
        if ($data === null) {
            $req_data = [
                    'chat_id' => $this->chat_id,
                    'text'    => 'Сurrently, there is no data to markup',
                ];
            return Request::sendMessage($req_data);
        }

        // TODO: We need to delete data with empty texts on Dataset upload,
        // because Telegram don't send/edit message with empty text!!
        if (!trim($data->data)) {
            $data->data = 'no data';
        }
        // For now, we just get the first labelGroup for dataset
        $labelGroup = $data->dataset->labelGroups[0];

        $rootLabel = Label::findOne([
            'label_group_id'  => $labelGroup->id,
            'parent_label_id' => 0
        ]);

        $inline_keyboard = new labelsKeyboard($rootLabel, $data->id, $this->moderator);

        $req_data = [
            'chat_id'                  => $this->chat_id,
            'text'                     => $data->data,
            'disable_web_page_preview' => true,
            'reply_markup'             => $inline_keyboard->generate(),
        ];

        if ($this->callback_query) {
            $req_data['message_id'] = $this->message_id;
            return Request::editMessageText($req_data);
        } else {
            return Request::sendMessage($req_data);
        }
    }

}
