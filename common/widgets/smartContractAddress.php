<?php
/**
 * Created by PhpStorm.
 * User: пользователь
 * Date: 21.04.2018
 * Time: 19:00
 */

namespace common\widgets;

use common\domain\ethereum\Etherscan;
use yii\base\Widget;
use Yii;

class smartContractAddress extends Widget
{
    public $address;
    public $linkText;

    public function init()
    {
        parent::init();
        if ($this->linkText === null) {
            $this->linkText = Yii::t('app', 'View in Etherscan');
        }
    }

    public function run()
    {
        return $this->render('smart-contract-address', [
            'address'  => $this->address,
            'link'     => Etherscan::addressUrl($this->address),
            'linkText' => $this->linkText
        ]);
    }
}