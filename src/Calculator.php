<?php

namespace App;

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
	}

	public function applyOperator(Operator $operator)
	{
		$operand2 = $this->stack->pop();
		$operand1 = $this->stack->pop();
		$result = $operator->apply( $operand1, $operand2 );
		$this->stack->push( $result );
	}

	public function resolveObject($string)
	{
		if( Operand::isValid($string) )
			return new Operand($string);
		else
			return OperatorFactory::make($string);
	}

	public function getValue()
	{
		return $this->stack->peek()->getValue();
	}
}
