<?php
/**
 * Created by PhpStorm.
 * User: bytecrow
 * Date: 21.04.2018
 * Time: 11:53
 */

namespace common\domain\ethereum;


class Etherscan
{
    const URL = 'https://rinkeby.etherscan.io';

    public static function addressUrl(string $address) : string
    {
        return (static::URL . '/address/' . $address);
    }

    public static function tokenUrl(string $tokenAddress) : string
    {
        return (static::URL . '/token/' . $tokenAddress);
    }
}
