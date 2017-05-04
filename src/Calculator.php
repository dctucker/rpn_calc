<?php

namespace App;

use App\Operand;
use App\Operator;

class Calculator
{
	public $warning = false;
	public function __construct(Stack $stack)
	{
		$this->stack = $stack;
	}

	public function push(Symbol $sym)
	{
		$this->warning = false;
		if( $sym instanceof Operator )
			return $this->applyOperator( $sym );
		elseif( $sym instanceof Operand )
			return $this->stack->push( $sym );
	}

	public function applyOperator(Operator $operator)
	{
		$stacksize = $this->stack->size();
		$missing = $operator->num_operands - $stacksize;
		if( $missing > 0 )
		{
			$this->warning = "Operator $operator requires $missing more on stack.";
			return false;
		}
		$operands = $this->stack->pop( $operator->num_operands );
		$result = $operator( ...$operands );
		return $this->stack->push( $result );
	}

	public function display()
	{
		return "".$this->stack->peek();
	}

	public function setComplexFormat($f)
	{
		Operands\Complex::$default_format = $f;
	}
}
