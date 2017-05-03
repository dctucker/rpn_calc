<?php

namespace App;

use App\OperandFactory;
use App\Operands\Scalar;

abstract class Operator
{
	public $identity;
	public $num_operands = 2;
	public function __construct($symbol)
	{
		$this->symbol = $symbol;
	}

	public function __toString()
	{
		return $this->symbol;
	}

	public function apply($operands)
	{
		$ret = $operands->current();
		for( $operands->next(); $operands->valid(); $operands->next() )
			$ret = $ret->operate( $this, $operands->current() );

		return $ret;
	}
	public abstract function scalars(Scalar $o1, Scalar $o2);
}
