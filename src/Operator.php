<?php

namespace App\Operators;

use App\OperandFactory;
use App\Operands\Scalar;

abstract class Operator
{
	public $identity;
	public $num_operands;
	public function __construct($symbol)
	{
		$this->symbol = $symbol;
	}

	public function __toString()
	{
		return $this->symbol;
	}

	public function generate($a)
	{
		if( is_array( $a ) )
			yield from $a;
		else
			yield $a;
	}

}

abstract class UnaryOperator extends Operator
{
	public $num_operands = 1;
	public abstract function scalar(Scalar $o1);

	public function __invoke($operand)
	{
		if( ! $operand instanceof \Generator )
			$operand = $this->generate( $operand );
		
		$ret = $operand->current();
		$ret = $ret->operate( $this );

		return $ret;
	}
}

abstract class BinaryOperator extends Operator
{
	public $num_operands = 2;
	public abstract function scalar(Scalar $o1, Scalar $o2);

	public function __invoke($operands)
	{
		if( ! $operands instanceof \Generator )
			$operands = $this->generate( $operands );

		$ret = $operands->current();
		for( $operands->next(); $operands->valid(); $operands->next() )
			$ret = $ret->operate( $this, $operands->current() );

		return $ret;
	}
}
