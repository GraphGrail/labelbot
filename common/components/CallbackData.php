<?php

namespace common\components;

use Yii;
use yii\web\HttpException;
use common\models\Moderator;

/**
 * CallbackData component
 * 
 * 
 */
class CallbackData extends yii\base\BaseObject
{
	/**
	 * @var int
	 */
	public $type;

	/**
	 * @var string
	 */
	public $sign;

	/**
	 * @var string
	 */
	public $data;

	/**
	 * @var common\models\Moderator
	 */
	public $moderator;

	/**
	 * Callback Data Types:
	 */
	const LABEL_KEY_PRESSED = 1;
	const BACK_KEY_PRESSED  = 2;

	/**
	 * Class constructor
	 * 
	 * @param Moderator $moderator 
	 * @param string|null $callback_data
	 */
	public function __construct(Moderator $moderator, string $callback_data=null)
	{
		$this->moderator = $moderator;

		if ($callback_data) {
			list(
				$this->type,
				$this->sign,
				$this->data
			) = explode(':', $callback_data, 3);
		}
	}

	/**
	 * Returns verified data
	 * @return string
	 */
	public function getVerifiedData() : string
	{
	    if (!$this->checkSign()) {
            throw new HttpException(400, 'Error verifing callback_data sign');
        }
        return $this->data;	
	}

	/**
	 * Returns signed CallbackData as string
	 * 
	 * @return string
	 */
	public function toString() : string
	{
		return $this->type .':'. $this->sign() .':'. $this->data;
	}

	/**
	 * Check, is data sign correct
	 * 
	 * @return bool
	 */
	public function checkSign() : bool
	{
		if ($this->sign === null) {
			throw new HttpException(500, 'Sign property must not be null for sign checking');
		}

		if ( hash_equals($this->sign, $this->sign()) ) {
            return true;
        }
		return false;
	}

	/**
	 * Returns sign of CallbackData
	 * @return type
	 */
	private function sign() : string
	{
		if ($this->type === null || $this->data === null) {
			throw new HttpException(500, 'Type and data properties must not be null to make sign');
		}

		return crypt($this->type . $this->data . $this->moderator->id, Yii::$app->params['telegram_bot_callback_secret_key']);
	}

	/**
	 * Returns type of specified CallbackData string
	 * @param string $callback_data 
	 * @return int
	 */
	public static function getType(string $callback_data) : int
	{
		list($type, $rest) = explode(':', $callback_data, 2);
		return $type;
	}

}