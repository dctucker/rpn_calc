<?php

namespace App\Operators;

use App\OperatorFactory;
use App\Operator;
use App\OperandFactory;
use App\Operand;
use App\Operands\Scalar;
use App\Operands\Complex;

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
			($c->imag)(),
		];
	}
}

class Plus extends BinaryOperator
{
	use AddComplex;
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return $o1() + $o2();
	}
}
class Minus extends BinaryOperator
{
	use AddComplex;
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return $o1() - $o2();
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

class Times extends BinaryOperator
{
	use ScaleComplex;
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return $o1() * $o2();
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
class Divide extends BinaryOperator
{
	use ScaleComplex;
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return $o1() / $o2();
	}

	public function complex(Complex $c2, Complex $c1)
	{
		$ac = ($c1->real)() * ($c2->real)();
		$bc = ($c1->imag)() * ($c2->real)();
		$ad = ($c1->real)() * ($c2->imag)();
		$bd = ($c1->imag)() * ($c2->imag)();
		$cc = ($c2->real)(); $cc *= $cc;
		$dd = ($c2->imag)(); $dd *= $dd;
		return [
			( $ac + $bd ) / ( $cc + $dd ),
			( $bc + $ad ) / ( $cc + $dd ),
		];
	}
}
class Power extends BinaryOperator
{
	public function scalar(Scalar $o2, Scalar $o1)
	{
		return pow($o1(), $o2()); // y^x, not x^y
	}
	public function scale(Complex $o2, Scalar $o1)
	{
		$mag = pow( $o2->mag(), $o1() );
		$arg = $o2->arg();
		return [
			$mag * cos( $o1() * $arg ),
			$mag * sin( $o1() * $arg ),
		];
	}
}
class Sqrt extends UnaryOperator
{
	public function scalar(Scalar $o)
	{
		return sqrt( $o() );
	}
}

class Reciprocal extends UnaryOperator
{
	public function scalar(Scalar $o)
	{
		return 1 / $o();
	}
	public function complex(Complex $o)
	{
		$xx = ($o->real)(); $xx *= $xx;
		$yy = ($o->imag)(); $yy *= $yy;
		return [
			  ($o->real)() / ( $xx + $yy ),
			- ($o->imag)() / ( $xx + $yy )
		];
	}
}

class Ln extends UnaryOperator
{
	public function scalar(Scalar $o)
	{
		return log( $o() );
	}
}

class NthLog extends BinaryOperator
{
	public function scalar(Scalar $s2, Scalar $s1)
	{
		return log( $s1(), $s2() );
	}
}

abstract class TrigOperator extends UnaryOperator
{
}

class Sin extends TrigOperator
{
	public function scalar(Scalar $o)
	{
		return sin( $o() );
	}

	public function complex(Complex $o)
	{
		return [
			sin( ($o->real)() ) * cosh( ($o->imag)() ),
			cos( ($o->real)() ) * sinh( ($o->imag)() )
		];
	}
}
class Cos extends TrigOperator
{
	public function scalar(Scalar $o)
	{
		return cos( $o() );
	}
	public function complex(Complex $o)
	{
		return [
			  cos( ($o->real)() ) * cosh( ($o->imag)() ),
			- sin( ($o->real)() ) * sinh( ($o->imag)() )
		];
	}
}
class Tan extends TrigOperator
{
	public function __construct()
	{
		$this->cos = OperatorFactory::make('cos');
		$this->sin = OperatorFactory::make('sin');
		$this->div = OperatorFactory::make('/');
	}
	public function scalar(Scalar $o)
	{
		return tan( $o() );
	}
	public function complex(Complex $o)
	{
		$components = [ ($this->cos)($o), ($this->sin)($o) ];
		return ($this->div)( $components );
	}
}

class Mag extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return abs( $s() );
	}
	public function complex(Complex $c)
	{
		return [
			$c->mag(),
			0
		];
	}
}

class Arg extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return atan2( 0, $s() );
	}
	public function complex(Complex $c)
	{
		return [
			$c->arg(),
			0
		];
	}
}

class Conj extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return $s();
	}
	public function complex(Complex $c)
	{
		return [
			  $c->real(),
			- $c->imag()
		];
	}
}
