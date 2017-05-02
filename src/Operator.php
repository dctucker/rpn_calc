<?php

namespace App;

abstract class Operator
{
	public function __construct($symbol)
	{
		$this->symbol = $symbol;
	}

	public function __toString()
	{
		return $this->symbol;
	}

	public function apply(Operand $o1, Operand $o2)
	{
		return new Operand( $this->operate($o1, $o2) );
	}
	public abstract function operate(Operand $o1, Operand $o2);
}
