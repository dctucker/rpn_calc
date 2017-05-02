<?php

namespace App;

class Operand
{
	public function __construct($value)
	{
		$this->value = $value;
	}

	public function __toString()
	{
		return "".$this->value;
	}

	public function getValue()
	{
		return $this->__toString();
	}

	public static function isValid($string)
	{
		return is_numeric( $string );
	}
}
