<?php

namespace common\interfaces;

use common\models\Task;
use common\domain\ethereum\Address;
use common\domain\ethereum\Contract;

interface BlockchainGatewayInterface
{
    public function walletAddress() : Address;
    public function checkBalances(Address $address, Address $tokenAddress): object;
    public function creditAccount(array $payload) : string;
    public function deployContract(Contract $contract) : string;
    public function contractStatus(Address $contractAddress): object;
    public function updateCompletedWork(Address $contractAddress, array $payload) : string;
    public function forceFinalize(Address $contractAddress) : string;
}
