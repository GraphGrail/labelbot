<?php

namespace common\components;

use common\models\Task;
use common\domain\ethereum\Address;
use common\domain\ethereum\Contract;
use yii\httpclient\Client;
use Yii;
use yii\log\Logger;

/**
 * EthereumGateway component
 */
class EthereumGateway extends yii\base\BaseObject implements \common\interfaces\BlockchainGatewayInterface
{
	protected $httpClient;
	protected $callbackUrl;

	public function __construct()
	{
		$this->callbackUrl = Yii::$app->params['ethGatewayCallbackUrl'];

		$this->httpClient = new Client([
			'baseUrl' => Yii::$app->params['ethGatewayApiUrl'],
		    'requestConfig' => [
		        'format' => Client::FORMAT_JSON
		    ],
		    'responseConfig' => [
		        'format' => Client::FORMAT_JSON
		    ],
		]);

		parent::__construct();	
	}

    public function walletAddress() : Address
    {
        return new Address($this->get('wallet-address')->address);
    }

    public function checkBalances(Address $address, Address $tokenAddress): object
    {
        return $this->get('check-balances', $address, '?tokenAddress='.$tokenAddress);
    }

    public function creditAccount(array $payload) : string
    {
        return $this->post('credit-account', null, $payload);
    }

    public function deployContract(Contract $contract) : string
    {
        return $this->post('deploy-contract', null, $contract);       
    }

    public function contractStatus(Address $contractAddress): object
    {
        return $this->get('contract-status', $contractAddress);
    }

    public function updateCompletedWork(Address $contractAddress, array $payload) : string
    {
        return $this->post('update-completed-work', $contractAddress, $payload);         
    }

    public function forceFinalize(Address $contractAddress) : string
    {
        return $this->post('force-finalize', $contractAddress);
    }

    /**
     * Helper method for get requests to eth gateway
     * 
     * @param string $api_method 
     * @param Address|null $address 
     * @return type
     */
    private function get(string $api_method, Address $address=null, string $otherParams='') // TODO: refact $otherParams to array
    {
        $param = ($address === null) ? '' : '/' . (string) $address;

        $res = $this->httpClient
            ->get($api_method . $param . $otherParams)
            ->send();
        if (!$res->isOk) {
            Yii::getLogger()->log($res->getContent(), Logger::LEVEL_ERROR);
            throw new \Exception("Can't call " . $api_method);
        }
        return json_decode($res->content);
    }

    /**
     * Helper method for post requests with callback to eth gateway
     * 
     * @param string $api_method 
     * @param Address|null $contractAddress 
     * @param array|object $payload 
     * @return string
     */
    private function post(string $api_method, $contractAddress, $payload=null) : string
    {
        $params = [];
        $params['callback'] = $this->callbackUrl . $api_method;

        if ($contractAddress !== null) {
            $params['contractAddress'] = (string) $contractAddress;
        }

        if ($payload !== null) {
            $params['payload'] = (object) $payload;
        }

        $res = $this->httpClient
                    ->post($api_method, $params)
                    ->send();

        if (!$res->isOk) {
            Yii::getLogger()->log($res->getContent(), Logger::LEVEL_ERROR);
            throw new \Exception("Can't call " . $api_method);
        }

        return $res->data['taskId'];
    }

}
