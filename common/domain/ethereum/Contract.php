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

	public function __construct(Address $clientAddress, int $totalWorkItems, int $workItemPrice = null)
	{
		$this->clientAddress  = (string) $clientAddress;
		$this->totalWorkItems = (string) $totalWorkItems;
		$this->workItemPrice  = $workItemPrice ? (string) $workItemPrice : Yii::$app->params['workItemPrice'];

		$this->tokenContractAddress 				   = Yii::$app->params['tokenContractAddress']; 
		$this->approvalCommissionBenificiaryAddress    = Yii::$app->params['approvalCommissionBenificiaryAddress'];
		$this->disapprovalCommissionBeneficiaryAddress = Yii::$app->params['disapprovalCommissionBeneficiaryAddress'];
		$this->approvalCommissionFraction 	 		   = Yii::$app->params['approvalCommissionFraction'];
		$this->disapprovalCommissionFraction 		   = Yii::$app->params['disapprovalCommissionFraction'];
		$this->autoApprovalTimeoutSec 				   = Yii::$app->params['autoApprovalTimeoutSec'];
	}

	// TODO: setters
}
