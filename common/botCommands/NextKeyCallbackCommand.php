<?php

namespace Longman\TelegramBot\Commands\UserCommands;

require_once 'AuthenticatedUserCommand.php';

use common\models\DataLabel;
use Longman\TelegramBot\Commands\AuthenticatedUserCommand;
use common\components\CallbackData;
use Longman\TelegramBot\Request;


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
     * @throws \yii\web\HttpException
     * @throws \yii\db\Exception
     */
    public function execute()
    {
        $callback_data = new CallbackData($this->moderator, $this->callback_query_data);
        $verified_callback_data = $callback_data->getVerifiedData();
        list($data_label_id, $label_id) = explode(':', $verified_callback_data);

        $dataLabel = DataLabel::findOne($data_label_id);

        if ($dataLabel === null || $dataLabel->status !== DataLabel::STATUS_NEW) {
            return $this->telegram->executeCommand('get');
        }

        if (!$dataLabel->skip()) {
            $req_data = [
                'callback_query_id' => $this->callback_query_id,
                'text'              => 'Can\'t skip this data. Please, choose label.',
                'show_alert'        => false,
                'cache_time'        => 0,
            ];
            Request::answerCallbackQuery($req_data);
        }

        return $this->telegram->executeCommand('get');
    }
}
