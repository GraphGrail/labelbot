<?php

namespace common\domain\ethereum;

/**
 * Ethereum Address Type
 */
class Address
{
	protected $address;

	public function __construct(string $address)
	{
		$this->address = $address;
		$this->validate();
	}

	public function __toString() : string
	{
		return strtolower($this->address);
	}

	private function validate()
	{
		if ( mb_strlen($this->address) != 42 || mb_substr($this->address, 0 , 2) != '0x') {
			throw new \Exception($this->address . ' is not valid ethereum address.');
		}
	}
}