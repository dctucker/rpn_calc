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
	public abstract function scalar(Scalar $s);

	public function __invoke(...$operand)
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
	public abstract function scalar(Scalar $s1, Scalar $s2);

	public function __invoke(...$operands)
	{
		$operands = $this->generate( $operands );

		$ret = $operands->current();
		for( $operands->next(); $operands->valid(); $operands->next() )
			$ret = $ret->operate( $this, $operands->current() );

		return $ret;
	}
}

trait AddComplex
{
	public function complex(Complex $c1, Complex $c2)
	{
		return [
			$this->scalar( $c1->real, $c2->real ),
			$this->scalar( $c1->imag, $c2->imag )
		];
	}

	public function scale(Complex $c, Scalar $s)
	{
		return [
			$this->scalar( $s, $c->real ),
			$c->imag(),
		];
	}
}

class Plus extends BinaryOperator
{
	use AddComplex;
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() + $s2();
	}
}
class Minus extends BinaryOperator
{
	use AddComplex;
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() - $s2();
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
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() * $s2();
	}
	public function complex(Complex $c1, Complex $c2)
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
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() / $s2();
	}

	public function complex(Complex $c1, Complex $c2)
	{
		$ac = $c1->real() * $c2->real();
		$bc = $c1->imag() * $c2->real();
		$ad = $c1->real() * $c2->imag();
		$bd = $c1->imag() * $c2->imag();
		$cc = $c2->real(); $cc *= $cc;
		$dd = $c2->imag(); $dd *= $dd;
		return [
			( $ac + $bd ) / ( $cc + $dd ),
			( $bc - $ad ) / ( $cc + $dd ),
		];
	}
}
class Power extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return pow($s1(), $s2()); // y^x, not x^y
	}
	public function scale(Complex $c, Scalar $s)
	{
		$mag = pow( $c->mag(), $s() );
		$arg = $c->arg();
		return [
			$mag * cos( $s() * $arg ),
			$mag * sin( $s() * $arg ),
		];
	}
}
class Sqrt extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return sqrt( $s() );
	}
}

class Reciprocal extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return 1 / $s();
	}
	public function complex(Complex $c)
	{
		$xx = $c->real(); $xx *= $xx;
		$yy = $c->imag(); $yy *= $yy;
		return [
			  $c->real() / ( $xx + $yy ),
			- $c->imag() / ( $xx + $yy )
		];
	}
}

class Ln extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return log( $s() );
	}
}

class NthLog extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return log( $s1(), $s2() );
	}
}

abstract class TrigOperator extends UnaryOperator
{
}

class Sin extends TrigOperator
{
	public function scalar(Scalar $s)
	{
		return sin( $s() );
	}

	public function complex(Complex $c)
	{
		return [
			sin( $c->real() ) * cosh( $c->imag() ),
			cos( $c->real() ) * sinh( $c->imag() )
		];
	}
}
class Cos extends TrigOperator
{
	public function scalar(Scalar $s)
	{
		return cos( $s() );
	}
	public function complex(Complex $c)
	{
		return [
			  cos( $c->real() ) * cosh( $c->imag() ),
			- sin( $c->real() ) * sinh( $c->imag() )
		];
	}
}
class Tan extends TrigOperator
{
	public function __construct($symbol)
	{
		parent::__construct($symbol);
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
		$operands = [ ($this->cos)($o), ($this->sin)($o) ];
		return ($this->div)( $operands );
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
