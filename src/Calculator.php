<?php

namespace App;

use App\Operand;
use App\Operator;

class Calculator
{
	public $warning = false;
	public $previous_stack;

	public function __construct(Stack $stack)
	{
		$this->stack = $stack;
	}

	/**
	 * enter a symbol into the calculator
	 * push onto stack or apply the given operator
	 */
	public function push(Symbol $sym)
	{
		$this->warning = false;
		if( $sym instanceof Operator )
			return $this->applyOperator( $sym );
		elseif( $sym instanceof Operand )
			return $this->stack->push( $sym );
	}

	/**
	 * pop the stack and apply the operator, issue warnings if needed
	 * @return integer or boolean, false if unsuccessful
	 */
	public function applyOperator(Operator $operator)
	{
		$this->backup();

		$stacksize = $this->stack->size();
		$missing = $operator->num_operands - $stacksize;
		if( $missing > 0 )
		{
			$this->warning = "Operator $operator requires $missing more on stack.";
			return false;
		}
		$operands = $this->stack->pop( $operator->num_operands );
		$result = $operator( ...$operands );
		if( $result === false )
		{
			$this->restore();
			$this->warning = "Operator '$operator' not supported for the given operands ($operator->required_interface)";
			return false;
		}
		return $this->stack->push( $result );
	}

	/**
	 * @return string the item on the top of the stack
	 */
	public function display()
	{
		return "".$this->stack->peek();
	}

	/**
	 * set format to be used by complex numbers
	 * @param $f string can be polar or rectangular
	 */
	public function setComplexFormat($f)
	{
		Operands\Complex::$default_format = $f;
	}

	/**
	 * remember the state of the current stack
	 */
	public function backup()
	{
		$this->previous_stack = clone $this->stack;
	}

	/**
	 * reset the stack to its previously backed-up state
	 */
	public function restore()
	{
		$this->stack = $this->previous_stack;
	}
}
