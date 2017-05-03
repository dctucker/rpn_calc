<?php

namespace App;

use App\Operand;
use App\Operator;

class Calculator
{
	public function __construct(Stack $stack)
	{
		$this->stack = $stack;
	}

	public function push($string)
	{
		$obj = $this->resolveObject($string);
		if( $obj instanceof Operator )
			$this->applyOperator( $obj );
		elseif( $obj instanceof Operand )
			$this->stack->push( $obj );
		return $obj;
	}

	public function applyOperator(Operator $operator)
	{
		$operands = $this->stack->pop( $operator->num_operands );
		$result = $operator( $operands );
		$this->stack->push( $result );
	}

	public function resolveObject($string)
	{
		if( OperandFactory::isValid($string) )
			return OperandFactory::make($string);
		else
			return OperatorFactory::make($string);
	}

	public function display()
	{
		return "".$this->stack->peek();
	}
}
