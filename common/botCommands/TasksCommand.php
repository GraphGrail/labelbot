<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\AuthenticatedUserCommand;
use Longman\TelegramBot\Request;
use common\components\LabelsKeyboard;
use common\models\Task;

/**
 * User "/getdata" command
 *
 * Display message with Data and inline keyboard with a Labels buttons.
 */
class TasksCommand extends AuthenticatedUserCommand
{
    /**
     * @var string
     */
    protected $name = 'tasks';

    /**
     * @var string
     */
    protected $description = 'Show list of tasks for label assignment';

    /**
     * @var string
     */
    protected $usage = '/tasks';

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
        $text = '';
        
        $availbleTasks = Task::find()
            ->where(['status'=>Task::STATUS_CONTRACT_ACTIVE])
            ->all();

        foreach ($availbleTasks as $task) {
            $text .= "/get_{$task->contract_address} - {$task->name}\n{$task->description}\n\n";
        }

        if ($availbleTasks === []) {
            $text = 'There is no tasks for now. Please try later.';
        }

        $data = [
            'chat_id'      => $this->chat_id,
            'text'         => $text,
        ];

        return Request::sendMessage($data);
    }
}
