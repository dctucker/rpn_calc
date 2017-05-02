<?php

namespace App\Operators;
use App\Operator;
use App\Operand;

class PlusOp extends Operator
{
	public function operate(Operand $o1, Operand $o2)
	{
		return $o1->getValue() + $o2->getValue();
	}
}
class MinusOp extends Operator
{
	public function operate(Operand $o1, Operand $o2)
	{
		return $o1->getValue() - $o2->getValue();
	}
}
class TimesOp extends Operator
{
	public function operate(Operand $o1, Operand $o2)
	{
		return $o1->getValue() * $o2->getValue();
	}
}
class DivideOp extends Operator
{
	public function operate(Operand $o1, Operand $o2)
	{
		return $o1->getValue() / $o2->getValue();
	}
}
class PowerOp extends Operator
{
	public function operate(Operand $o1, Operand $o2)
	{
		return pow($o1->getValue(), $o2->getValue());
	}
}
