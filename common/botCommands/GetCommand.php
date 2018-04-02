<?php

namespace Longman\TelegramBot\Commands\UserCommands;


use Longman\TelegramBot\Commands\AuthenticatedUserCommand;
use Longman\TelegramBot\Request;
use common\models\Moderator;
use common\models\Task;
use common\domain\ethereum\Address;


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
    protected $description = 'Get command';

    /**
     * @var string
     */
    protected $usage = '/Get <contract_address>';

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
     */
    public function execute()
    {
 		$message = $this->getMessage();

        $contract_address = $command !== 'get' 
        				  ? substr($command, 4)
        				  : trim($message->getText(true));

        // $contract_address ethereum address validation  

        $task = Task::find()
        	->where(['contract_address'=>$contract_address])
        	->active()
        	->one();

        if ($task === null) {
            $req_data = [
                    'chat_id' => $this->chat_id,
                    'text'    => 'Inactive task. Please try to get data for this tsk later.',
                ];
            return Request::sendMessage($req_data);        	
        }

        $data = $task->getDataForLabelAssignment($this->moderator->id); //Data::getForLabelAssignment(1, $this->moderator->id);
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

        $rootLabel = Label::findOne([
            'label_group_id'  => $task->label_group_id,
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
        }

        return Request::sendMessage($req_data);
    }

}






