<?php

namespace App\Operators;
use App\Operators\Operator;
use App\Operands\Operand;
use App\Operands\Scalar;
use App\Operands\Complex;
use App\OperandFactory;
use App\OperatorFactory;

trait AddComplex
{
	public function complex(Complex $o2, Complex $o1)
	{
		return [
			$this->scalar( $o1->real, $o2->real ),
			$this->scalar( $o1->imag, $o2->imag )
		];
	}

	public function scale(Complex $c, Scalar $s)
	{
		return [
			$this->scalar( $s, $c->real ),
			$c->imag->value,
		];
	}
}

class PlusOp extends BinaryOperator
{
	use AddComplex;
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return $o1->getValue() + $o2->getValue();
	}
}
class MinusOp extends BinaryOperator
{
	use AddComplex;
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return $o1->getValue() - $o2->getValue();
	}
}

trait ScaleComplex
{
	public function scale(Complex $c, Scalar $s)
	{
		return [
			$this->scalar( $s, $c->real ),
			$this->scalar( $s, $c->imag )
		];
	}
}

class TimesOp extends BinaryOperator
{
	use ScaleComplex;
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return $o1->getValue() * $o2->getValue();
	}
	public function complex(Complex $c2, Complex $c1)
	{
		$ac = $this->scalar( $c1->real, $c2->real );
		$bd = $this->scalar( $c1->imag, $c2->imag );
		$ad = $this->scalar( $c1->real, $c2->imag );
		$bc = $this->scalar( $c1->imag, $c2->real );
		return [
			$ac - $bd ,
			$ad + $bc ,
		];
	}
}
class DivideOp extends BinaryOperator
{
	use ScaleComplex;
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return $o1->getValue() / $o2->getValue();
	}

	public function complex(Complex $c2, Complex $c1)
	{
		$ac = $c1->real->getValue() * $c2->real->getValue();
		$bc = $c1->imag->getValue() * $c2->real->getValue();
		$ad = $c1->real->getValue() * $c2->imag->getValue();
		$bd = $c1->imag->getValue() * $c2->imag->getValue();
		$cc = $c2->real->getValue(); $cc *= $cc;
		$dd = $c2->imag->getValue(); $dd *= $dd;
		return [
			( $ac + $bd ) / ( $cc + $dd ),
			( $bc + $ad ) / ( $cc + $dd ),
		];
	}
}
class PowerOp extends BinaryOperator
{
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return pow($o2->getValue(), $o1->getValue());
	}
	public function scale(Complex $o2, Scalar $o1)
	{
		$mag = pow( $o2->mag(), $o1->getValue() );
		$arg = $o2->arg();
		return [
			$mag * cos( $o1->getValue() * $arg ),
			$mag * sin( $o1->getValue() * $arg ),
		];
	}
}
class SqrtOp extends UnaryOperator
{
	public function scalar(Scalar $o)
	{
		return sqrt( $o->getValue() );
	}
}

abstract class TrigOperator extends UnaryOperator
{
}

class SinOp extends TrigOperator
{
	public function scalar(Scalar $o)
	{
		return sin( $o->getValue() );
	}

	public function complex(Complex $o)
	{
		return [
			sin( $o->real->getValue() ) * cosh( $o->imag->getValue() ),
			cos( $o->real->getValue() ) * sinh( $o->imag->getValue() )
		];
	}
}
class CosOp extends TrigOperator
{
	public function scalar(Scalar $o)
	{
		return cos( $o->getValue() );
	}
	public function complex(Complex $o)
	{
		return [
			  cos( $o->real->getValue() ) * cosh( $o->imag->getValue() ),
			- sin( $o->real->getValue() ) * sinh( $o->imag->getValue() )
		];
	}
}
class TanOp extends TrigOperator
{
	public function __construct()
	{
		$this->cos = OperatorFactory::make('cos');
		$this->sin = OperatorFactory::make('sin');
		$this->div = OperatorFactory::make('/');
	}
	public function scalar(Scalar $o)
	{
		return tan( $o->getValue() );
	}
	public function complex(Complex $o)
	{
		$components = [ ($this->cos)($o), ($this->sin)($o) ];
		return ($this->div)( $components );
	}
}

class MagOp extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return abs( $s->getValue() );
	}
	public function complex(Complex $c)
	{
		return [
			$c->mag(),
			0
		];
	}
}
