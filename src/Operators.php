<?php

namespace App\Operators;

use App\OperatorFactory;
use App\Operator;
use App\OperandFactory;
use App\Operand;
use App\Operands\Scalar;
use App\Operands\Complex;

interface StackOperator   {}
interface ComplexOperator {}
interface ScalarOperator  {}
interface UnaryScalar   extends ScalarOperator  { public function scalar(Scalar $s); }
interface BinaryScalar  extends ScalarOperator  { public function scalar(Scalar $s1, Scalar $s2); }
interface UnaryComplex  extends ComplexOperator { public function complex(Complex $c); }
interface BinaryComplex extends ComplexOperator { public function complex(Complex $c1, Complex $c2); }
interface BinaryComplexScalar  extends ComplexOperator { public function scale(Complex $c, Scalar $s); }

abstract class UnaryOperator extends Operator implements UnaryScalar
{
	public $num_operands = 1;

	/**
	 * take a single operand and apply operator to it
	 * @param $operands iterable of operand(s) - only first is used
	 * @return Operand
	 */
	public function __invoke(...$operands)
	{
		$operands = $this->generate( $operands );
		
		$ret = $operands->current();
		$ret = $ret->operate( $this );

		return $ret;
	}
}

abstract class BinaryOperator extends Operator implements BinaryScalar
{
	public $num_operands = 2;

	/**
	 * take operands and apply operator to them in sequence
	 * @param $operands iterable of operands
	 * @return Operand
	 */
	public function __invoke(...$operands)
	{
		$operands = $this->generate( $operands );

		$ret = $operands->current();
		for( $operands->next(); $operands->valid(); $operands->next() )
		{
			$ret = $ret->operate( $this, $operands->current() );
		}

		return $ret;
	}
}

// stack functions

class Pop extends Operator implements StackOperator
{
	public $num_operands = 1;
	/**
	 * incoming operand(s) will the thrown away.
	 * @param $operand Generator of items to discard
	 * @return void
	 */
	public function __invoke(...$operand)
	{
		// NOP
	}
}

class Swap extends Operator implements StackOperator
{
	public $num_operands = 2;
	/**
	 * @param @operands Generator of items
	 * @return Generator of items in reverse order
	 */
	public function __invoke(...$operands)
	{
		$operands = $this->generate( $operands );
		yield from array_reverse( iterator_to_array( $operands ) );
	}
}


// arithmetic operations

trait AddComplex
{
	public function complex(Complex $c1, Complex $c2)
	{
		return [
			$this->scalar( $c1->real, $c2->real ),
			$this->scalar( $c1->imag, $c2->imag )
		];
	}

	/**
	 * add the given Scalar to the real part of the given Complex number
	 * @return array data for constructing a new Complex
	 */
	public function scale(Complex $c, Scalar $s)
	{
		return [
			$this->scalar( $s, $c->real ),
			$c->imag(),
		];
	}
}

class Plus extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
{
	use AddComplex;
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() + $s2();
	}
}
class Minus extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
{
	use AddComplex;
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() - $s2();
	}
}

trait ScaleComplex
{
	/**
	 * multiply both components of the given Complex by the given Scalar
	 * @return data for constructing a new Complex
	 */
	public function scale(Complex $c, Scalar $s)
	{
		return [
			$this->scalar( $s, $c->real ),
			$this->scalar( $s, $c->imag )
		];
	}
}

class Times extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
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
class Divide extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
{
	use ScaleComplex;
	public function scalar(Scalar $s1, Scalar $s2)
	{
		if( $s2() == 0 )
			return NAN;
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
		if( $cc + $dd == 0 )
			return [NAN,NAN];
		return [
			($ac + $bd) / ($cc + $dd),
			($bc - $ad) / ($cc + $dd)
		];
	}
}
class Reciprocal extends UnaryOperator implements UnaryComplex
{
	public function scalar(Scalar $s)
	{
		if( $s() == 0 )
			return NAN;
		return 1 / $s();
	}
	public function complex(Complex $c)
	{
		$xx = $c->real(); $xx *= $xx;
		$yy = $c->imag(); $yy *= $yy;
		if( $xx + $yy == 0 )
			return [NAN,NAN];
		return [
			  $c->real() / ( $xx + $yy ),
			- $c->imag() / ( $xx + $yy )
		];
	}
}
class Negative extends UnaryOperator implements UnaryComplex
{
	public function scalar(Scalar $s)
	{
		return - $s();
	}
	public function complex(Complex $c)
	{
		return [
			- $c->real(),
			- $c->imag()
		];
	}
}


// exponentation operations

class Power extends BinaryOperator implements BinaryComplexScalar
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
class Sqrt extends UnaryOperator // @TODO implements UnaryComplex
{
	public function scalar(Scalar $s)
	{
		if( $s() < 0 )
			return new Complex(
				0,
				sqrt( abs( $s() ) )
			);
		return sqrt( $s() );
	}
}

class Ln extends UnaryOperator implements UnaryComplex
{
	public function scalar(Scalar $s)
	{
		return log( $s() );
	}
	public function complex(Complex $c)
	{
		return [
			log( $c->mag() ),
			$c->arg()
		];
	}
}

class NthLog extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return log( $s1(), $s2() );
	}
}

// trigonometric operations

abstract class TrigOperator extends UnaryOperator
{
}

class Sin extends TrigOperator implements UnaryComplex
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
class Cos extends TrigOperator implements UnaryComplex
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
class Tan extends TrigOperator implements UnaryComplex
{
	public function __construct($symbol)
	{
		parent::__construct($symbol);
		$this->cos = OperatorFactory::make('cos');
		$this->sin = OperatorFactory::make('sin');
		$this->div = OperatorFactory::make('/');
	}
	public function scalar(Scalar $s)
	{
		return tan( $s() );
	}
	public function complex(Complex $c)
	{
		return $this->div( $this->sin($c) , $this->cos($c) );
	}
}

// complex-oriented operations

class Mag extends UnaryOperator implements UnaryComplex
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

class Arg extends UnaryOperator implements UnaryComplex
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

class Conj extends UnaryOperator implements UnaryComplex
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
