<?php

namespace common\domain\ethereum;

use common\domain\ethereum\Address;

/**
 * Ethereum Contract Type
 */
class Contract
{
	/**
	 * Адрес контракта ERC-20 токена, которым платят заразметку
	 */
	public $tokenContractAddress;
	/**
	 * Адрес кошелька заказчика, который будет активировать контракт
	 */
	public $clientAddress;
	/** 
	 * Адрес кошелька, на который уходит комиссия за принятые задачи по разметке
	 */
	public $approvalCommissionBenificiaryAddress; 
	/**
	 * Адрес кошелька, на который уходит комиссия за отклоненные задачи по разметке
	 */
	public $disapprovalCommissionBeneficiaryAddress;
	/**
	 * Доля оплаты за выполненную задачу, которая отчисляется в виде комиссии за принятую задачу по разметке
	 */
	public $approvalCommissionFraction;
	/**
	 * Доля оплаты за выполненную задачу, которая отчисляется в виде комиссии за отклоненную задачу по разметке
	 */
	public $disapprovalCommissionFraction;
	/**
	 * Количество единиц работы (если мы оплачиваем чанки по 10 элементов — totalWorkItems это количество десятков элементов)
	 */
	public $totalWorkItems;
	/**
	 * Стоимость единицы работы
	 */
	public $workItemPrice;
	/**
	 * Время в секундах, по истечению которого контракту можно сделать force-stop
	 */
	public $autoApprovalTimeoutSec;

	public function __construct(Address $clientAddress, int $totalWorkItems, int $workItemPrice = 1000000000000000000)
	{
		$this->clientAddress  = (string) $clientAddress;
		$this->totalWorkItems = (string) $totalWorkItems;
		$this->workItemPrice  = (string) $workItemPrice;

		// TODO: config for all this params
		$this->tokenContractAddress 				   = '0x11e0892806ab9fd37224a2031c51156968c2ee72'; 
		$this->approvalCommissionBenificiaryAddress    = '0x24a8dcf36178e239134ce89f74b45d734b5780f8';
		$this->disapprovalCommissionBeneficiaryAddress = '0xe354a075b40ce98f1e1b377c0420020f358f2e48';
		$this->approvalCommissionFraction 	 = '0.1';
		$this->disapprovalCommissionFraction = '0.2';
		$this->autoApprovalTimeoutSec = '60';
	}

	// TODO: setters
}
