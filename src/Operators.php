<?php

namespace App\Operators;
use App\Operator;
use App\Operand;
use App\Operands\Scalar;
use App\Operands\Complex;
use App\OperandFactory;

trait AddComplex
{
	public function complex(Complex $o1, Complex $o2)
	{
		return [
			$this->scalars( $o1->real, $o2->real ),
			$this->scalars( $o1->imag, $o2->imag )
		];
	}
	public function scale(Complex $o1, Scalar $o2)
	{
		$o1->real->operate( $this, $o2 );
		return $o1;
	}
}

class PlusOp extends Operator
{
	use AddComplex;
	public function scalars(Scalar $o1, Scalar $o2)
	{
		return $o1->getValue() + $o2->getValue();
	}
}
class MinusOp extends Operator
{
	use AddComplex;
	public function scalars(Scalar $o1, Scalar $o2)
	{
		return $o1->getValue() - $o2->getValue();
	}
}
class TimesOp extends Operator
{
	public function scalars(Scalar $o1, Scalar $o2)
	{
		return $o1->getValue() * $o2->getValue();
	}
	public function complex(Complex $o1, Complex $o2)
	{
		$ac = $this->scalars( $o1->real, $o2->real );
		$bd = $this->scalars( $o1->imag, $o2->imag );
		$ad = $this->scalars( $o1->real, $o2->imag );
		$bc = $this->scalars( $o1->imag, $o2->real );
		return [
			$ac - $bd ,
			$ad + $bc ,
		];
	}
	public function scale(Complex $o1, Scalar $o2)
	{
		$o1->real->operate( $this, $o2 );
		$o1->imag->operate( $this, $o2 );
		return $o1;
	}
}
class DivideOp extends Operator
{
	public function scalars(Scalar $o1, Scalar $o2)
	{
		return $o1->getValue() / $o2->getValue();
	}
}
class PowerOp extends Operator
{
	public function scalars(Scalar $o1, Scalar $o2)
	{
		return pow($o1->getValue(), $o2->getValue());
	}
}
class SqrtOp extends PowerOp
{
	public $num_operands = 1;
	public function apply($operand)
	{
		return OperandFactory::make( sqrt($operand->current()->getValue()) );
	}
}
