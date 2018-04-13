<?php

namespace Longman\TelegramBot\Commands\UserCommands;

require_once 'AuthenticatedUserCommand.php';

use Longman\TelegramBot\Commands\AuthenticatedUserCommand;
use Longman\TelegramBot\Request;
use common\models\Task;
use Yii;

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

        $reward = bcdiv(Yii::$app->params['workItemPrice'], '1000000000000000000', 4);

        foreach ($availbleTasks as $task) {
            $text .= "{$task->name} /get_{$task->contract_address}" . PHP_EOL
                . "Total: {$task->total_work_items} items, each {$task->work_item_size} texts to label. "
                . "Reward: {$reward} GAI for item." . PHP_EOL;
            if ($task->description) {
                $text .= "Description: {$task->description}" . PHP_EOL;
            }
            $text .= PHP_EOL;
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
