<?php

namespace Longman\TelegramBot\Commands\SystemCommands;


use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use common\models\Moderator;
use common\domain\ethereum\Address;


/**
 * Login command
 *
 */
class LoginCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'login';

    /**
     * @var string
     */
    protected $description = 'Login command';

    /**
     * @var string
     */
    protected $usage = '/login <token>';

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
        $message = $this->getMessage();
        $chat_id = $this->getMessage()->getChat()->getId();

        $param = trim($message->getText(true));
        $text  = $param 
               ? $this->registerModerator($param)
               : 'Before you get started, please enter your Ethereum wallet address to login with /login <wallet_address> command.';

        $data = [
            'chat_id'      => $chat_id,
            'text'         => $text,
        ];
        //return Request::sendMessage($data);

        //TODO: do something with it
        Request::sendMessage($data);
        die();
    }


    private function registerModerator(string $walletAddress) : string
    {
        try {
            $address = new Address($walletAddress);
        } catch (\Exception $e) {
            return 'Login error: check that entered Ethereum wallet address is valid.';
        }

        $from = $this->getMessage()->getFrom();

        // Checks that user don't already registered
        $isNotRegistered = Moderator::findOne(['tg_id'=>$from->getId()]);
        if ($isNotRegistered !== null) {
            return "This telegram account is alredy registered with Ethereum wallet $isNotRegistered->eth_addr.";
        }

        // Checks that this address not in use on server
        $isAddressNotUsed = Moderator::findOne(['eth_addr'=>$address]);
        if ($isAddressNotUsed !== null) {
            return "Ethereum wallet address $address is already used by another telegram account. Please use another ethereum wallet address.";
        }

        $moderator = new Moderator;
        $moderator->eth_addr      = (string) $address;
        $moderator->tg_id         = $from->getId();
        $moderator->tg_username   = $from->getUsername();
        $moderator->tg_first_name = $from->getFirstName();
        $moderator->tg_last_name  = $from->getLastName();

        if (!$moderator->save()) {
            return "Login error.";
        }

        return "You succesfuly logined with Ethereum wallet address $address.\nEnter /help to see all commands.";
    }


}
