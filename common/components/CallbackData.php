<?php

namespace common\components;

use Yii;
use yii\web\HttpException;
use common\models\Moderator;


class CallbackData extends yii\base\BaseObject
{
	public $type;
	public $sign;
	public $data;
	public $moderator;

	const LABEL_BUTTON = 1;

	public function __construct(Moderator $modertor, string $callback_data=null)
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

	public function toString() : string
	{
		return $this->type .':'. $this->sign() .':'. $this->data;
	}

	public function checkSign() : bool
	{
		if ($this->sign === null) {
			throw new HttpException(500, 'Need sign property to be not null to check sign');
		}

		if ( hash_equals($this->sign, $this->sign()) ) {
            return true;
        }
		return false;
	}

	private function sign() : string
	{
		if ($this->type === null || $this->data === null) {
			throw new HttpException(500, 'Need type and data properties to be not null to make sign');
		}

		return crypt($this->type . $this->data . $this->moderator->id, Yii::$app->params['telegram_bot_callback_secret_key']);
	}

}