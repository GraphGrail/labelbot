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

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use common\components\Bot;

/**
 * User "/getdata" command
 *
 * Display an inline keyboard with a few buttons.
 */
class GetdataCommand extends UserCommand
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
        $bot = new Bot($this);
        return $bot->sendData();
    }

}
