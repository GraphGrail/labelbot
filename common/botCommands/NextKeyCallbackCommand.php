<?php

namespace Longman\TelegramBot\Commands\UserCommands;

require_once 'AuthenticatedUserCommand.php';

use Longman\TelegramBot\Commands\AuthenticatedUserCommand;
use Longman\TelegramBot\Request;
use common\components\CallbackData;
use common\models\Label;
use common\models\AssignedLabel;


/**
 * NextKey callback command
 *
 * This command handles callback queries with NEXT_KEY_PRESSED type.
 *
 * @see CallbackqueryCommand.php
 */
class NextKeyCallbackCommand extends AuthenticatedUserCommand
{
    /**
     * @var string
     */
    protected $name = 'nextkeycallback';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $usage = '/nextkeycallback';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * @var bool
     */
    public $hidden = true;

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
        list($assigned_label_id, $label_id) = explode(':', $verified_callback_data);

        $assignedLabel = AssignedLabel::findOne($assigned_label_id);

        if ($assignedLabel === null || $assignedLabel->status != AssignedLabel::STATUS_IN_HAND) {
            return $this->telegram->executeCommand('get');
        }

        $assignedLabel->status = AssignedLabel::STATUS_SKIPPED;            
        if (!$assignedLabel->save()) {
            // TODO: log error
        }

        return $this->telegram->executeCommand('get');
    }
}
