<?php

namespace Longman\TelegramBot\Commands\UserCommands;

require_once 'AuthenticatedUserCommand.php';

use Longman\TelegramBot\Commands\AuthenticatedUserCommand;
use Longman\TelegramBot\Request;
use common\domain\ethereum\Address;
use common\models\Task;
use common\models\Data;
use common\models\Label;
use common\components\LabelsKeyboard;


/**
 * Get command
 */
class GetCommand extends AuthenticatedUserCommand
{
    /**
     * @var string
     */
    protected $name = 'select';

    /**
     * @var string
     */
    protected $description = 'Get data for labeling command';

    /**
     * @var string
     */
    protected $usage = '/Get';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    public $hidden = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     * @throws \yii\web\HttpException
     */
    public function execute()
    {
        $command = $this->message->getCommand();

        if (substr($command, 0, 6) === 'get_0x') {
            $command_param = substr($command, 4);

            try {
                $contract_address = new Address($command_param);
            } catch (\Exception $e) {
                $req_data = [
                    'chat_id' => $this->chat_id,
                    'text' => 'Wrong contract address.',
                ];
                return Request::sendMessage($req_data);
            }

            $this->moderator->current_task = (string)$contract_address;
            $this->moderator->save();
        }

        if (!$this->moderator->current_task) {
            return $this->telegram->executeCommand('tasks');
        }

        $task = Task::find()
            ->where(['contract_address' => $this->moderator->current_task])
            ->active()
            ->one();

        if ($task === null) {
            $req_data = [
                'chat_id' => $this->chat_id,
                'text' => 'Inactive task. Please, try to get data for this task later.',
            ];
            return Request::sendMessage($req_data);
        }

        // We don't let to get own tasks works because it going to errors with smart contract
        $task_contract = json_decode($task->contract);
        if ($task_contract->clientAddress === $this->moderator->eth_addr) {
            $req_data = [
                'chat_id' => $this->chat_id,
                'text' => 'Sorry, but you can\'t get own tasks jobs.',
            ];
            return Request::sendMessage($req_data);
        }

        $dataLabel = $task->getDataForLabelAssignment($this->moderator);

        if ($dataLabel === null) {
            $req_data = [
                'chat_id' => $this->chat_id,
                'text' => 'Сurrently, there is no data to markup in this task. Please, try to get data for this task later.',
            ];
            return Request::sendMessage($req_data);
        }

        $data = Data::findOne($dataLabel->data_id);
        // TODO: We need to delete data with empty texts on Dataset upload,
        // because Telegram don't send/edit message with empty text!!
        if (!trim($data->data)) {
            $data->data = 'no data';
        }

        $rootLabel = Label::findOne([
            'label_group_id' => $task->label_group_id,
            'parent_label_id' => 0
        ]);

        $inline_keyboard = new LabelsKeyboard($rootLabel, $dataLabel, $this->moderator);

        $req_data = [
            'chat_id' => $this->chat_id,
            'text' => $data->data,
            'disable_web_page_preview' => true,
            'reply_markup' => $inline_keyboard->generate(),
        ];

        if ($this->callback_query) {
            $req_data['message_id'] = $this->message_id;
            return Request::editMessageText($req_data);
        }

        return Request::sendMessage($req_data);
    }

}

