<?php

namespace App;

class Operator
{
	protected static $valid_operators = [
		'+',
		'-',
		'*',
		'/',
	];

	public function __construct($operator)
	{
		$this->operator = $operator;
	}

	public function __toString()
	{
		return $this->operator;
	}

	public function operate(Operand $o1, Operand $o2)
	{
		switch( $this->operator )
		{
			case '+': return $o1->getValue() + $o2->getValue();
			case '-': return $o1->getValue() - $o2->getValue();
			case '*': return $o1->getValue() * $o2->getValue();
			case '/': return $o1->getValue() / $o2->getValue();
		}
	}

	public static function isValid($string)
	{
		return in_array( $string, static::$valid_operators );
	}
}
