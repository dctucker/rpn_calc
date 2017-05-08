<?php

namespace App\Operators;

use App\OperatorFactory;
use App\Operator;
use App\OperandFactory;
use App\Operand;
use App\Operands\Scalar;
use App\Operands\BaseScalar;
use App\Operands\Complex;
use App\Operands\PolarComplex;
use App\Notations\Degrees;
use App\Notations\Binary;
use App\Notations\Octal;
use App\Notations\Decimal;
use App\Notations\Hexadecimal;



interface StackOperator   {}
interface ComplexOperator {}
interface ScalarOperator  {}
interface UnaryScalar   extends ScalarOperator  { public function scalar(Scalar $s); }
interface BinaryScalar  extends ScalarOperator  { public function scalar(Scalar $s1, Scalar $s2); }
interface UnaryComplex  extends ComplexOperator { public function complex(Complex $c); }
interface BinaryComplex extends ComplexOperator { public function complex(Complex $c1, Complex $c2); }
interface BinaryComplexScalar  extends ComplexOperator
{
	public function scalarComplex(Scalar $c, Complex $s);
	public function complexScalar(Complex $c, Scalar $s);
}

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
		if( $operands->valid() )
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
			$ret = $ret->operate( $this, $operands->current() );
		return $ret;
	}
}

// stack operations

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

class Push extends Operator implements StackOperator
{
	public $num_operands = 1;
	/**
	 * incoming operand will duplicated.
	 * @param $operand mixed item to duplicate
	 * @return Generator
	 * @codeCoverageIgnore
	 */
	public function __invoke(...$operands)
	{
		$operands = $this->generate($operands);
		$operand = $operands->current();
		if( $operand )
		{
			yield $operand;
			yield $operand;
		}
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
}

class Plus extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
{
	use AddComplex;
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() + $s2();
	}
	public function complexScalar(Complex $c, Scalar $s)
	{
		return [
			$c->real() + $s(),
			$c->imag(),
		];
	}
	public function scalarComplex(Scalar $s, Complex $c)
	{
		return new Complex([
			$s() + $c->real(),
			$c->imag(),
		]);
	}
}
class Minus extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
{
	use AddComplex;
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() - $s2();
	}
	public function complexScalar(Complex $c, Scalar $s)
	{
		return [
			$c->real() - $s(),
			$c->imag(),
		];
	}
	public function scalarComplex(Scalar $s, Complex $c)
	{
		return new Complex([
			$s() - $c->real(),
			- $c->imag(),
		]);
	}
}

class Times extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() * $s2();
	}
	public function complex(Complex $c1, Complex $c2)
	{
		$ac = $c1->real() * $c2->real();
		$bd = $c1->imag() * $c2->imag();
		$ad = $c1->real() * $c2->imag();
		$bc = $c1->imag() * $c2->real();
		return [
			$ac - $bd ,
			$ad + $bc ,
		];
	}
	/**
	 * multiply both components of the given Complex by the given Scalar
	 * @return data for constructing a new Complex
	 */
	public function complexScalar(Complex $c, Scalar $s)
	{
		return [
			$this->scalar( $s, $c->real ),
			$this->scalar( $s, $c->imag )
		];
	}
	public function scalarComplex(Scalar $s, Complex $c)
	{
		return new Complex( $this->complexScalar($c, $s) ); // commutative
	}
}
class Divide extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
{
	public function __construct($symbol)
	{
		parent::__construct($symbol);
		$this->times = OperatorFactory::make('*');
		$this->recip = OperatorFactory::make('1/x');
	}
	public function scalar(Scalar $s1, Scalar $s2)
	{
		if( $s2() == 0 )
			return NAN;
		return $this->times->scalar( $s1, $this->recip( $s2 ) );
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
			($ac + $bd) / ( $cc + $dd ),
			($bc - $ad) / ( $cc + $dd )
		];
	}

	public function scalarComplex(Scalar $s, Complex $c)
	{
		return  $this->times->scalarComplex( $s, $c->operate($this->recip) );
	}

	public function complexScalar(Complex $c, Scalar $s)
	{
		return $this->times->complexScalar( $c, $this->recip( $s ) );
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

class Modulo extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		if( $s2() == 0 )
			return NAN;
		return fmod( $s1(), $s2() );
	}
}

class Intval extends UnaryOperator
{
	public function scalar(Scalar $s1)
	{
		return intval($s1());
	}
}

class Frac extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return $s() - floor($s());
	}
}

class Round extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return round($s());
	}
}


// base conversion operations

abstract class BaseOperator extends UnaryOperator
{
	use \App\Notations\Base;
	public function scalar(Scalar $s)
	{
		$string = $this->baseSymbol( $s() );
		return OperandFactory::make( $string );
	}
}

class Bin extends BaseOperator { use Binary; }
class Oct extends BaseOperator { use Octal; }
class Dec extends BaseOperator { use Decimal; }
class Hex extends BaseOperator { use Hexadecimal; }

class Dump extends UnaryOperator implements UnaryComplex
{
	public function scalar(Scalar $s)
	{
		var_dump($s);
		return $s;
	}
	public function complex(Complex $c)
	{
		var_dump($c);
		return $c;
	}
}

// bitwise operations

class BAnd extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() & $s2();
	}
}

class BOr extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() | $s2();
	}
}

class BXor extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() ^ $s2();
	}
}

class BNot extends UnaryOperator
{
	public function scalar(Scalar $s)
	{
		return $s->bnot();
	}
}

class BShiftLeft extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() << $s2();
	}
}

class BShiftRight extends BinaryOperator
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() >> $s2();
	}
}

// exponentation operations

class Power extends BinaryOperator implements BinaryComplex, BinaryComplexScalar
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return $s1() ** $s2(); // y^x, not x^y
	}
	public function complex(Complex $c1, Complex $c2)
	{
 		$aabb = $c1->real() ** 2 + $c1->imag() ** 2;
		$mag = $aabb ** ($c2->real() / 2) * exp( -$c2->imag() * $c1->arg() );
		$arg = $c2->real() * $c1->arg() + 0.5 * $c2->imag() * log( $aabb );
		return new PolarComplex($mag, $arg);
	}
	public function complexScalar(Complex $c, Scalar $s)
	{
		return $this->complex( $c, new Complex( $s, OperandFactory::make('0') ) );
	}
	public function scalarComplex(Scalar $s, Complex $c)
	{
		return $this->complex( OperandFactory::make($s().'+0i'), $c );
	}
}
class Sqrt extends UnaryOperator implements UnaryComplex
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

	public function complex(Complex $c)
	{
		$aa = $c->real(); $aa *= $aa;
		$bb = $c->imag(); $bb *= $bb;
		$sq = sqrt( $aa + $bb );
		$sign = $c->imag() <=> 0;
		return [
			sqrt( (   $c->real() + $sq ) / 2 ),
			sqrt( ( - $c->real() + $sq ) / 2 ) * $sign
		];
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

class NthLog extends BinaryOperator implements BinaryComplexScalar
{
	public function scalar(Scalar $s1, Scalar $s2)
	{
		return log( $s1(), $s2() );
	}
	public function complexScalar(Complex $c, Scalar $s)
	{
		return [
			log( $c->mag() ) / log( $s() ),
			$c->arg() / log( $s() )
		];
	}
	public function scalarComplex(Scalar $s, Complex $c)
	{
		$div = OperatorFactory::make('/');
		$ln = OperatorFactory::make('ln');
		return $div( $s->operate($ln), $c->operate($ln) );
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

class Degree extends TrigOperator
{
	use Degrees;
	public function scalar(Scalar $s)
	{
		return OperandFactory::make( $this->degSymbol( $s() ) );
	}
}

class Radian extends TrigOperator
{
	public function scalar(Scalar $s)
	{
		return OperandFactory::make($s());
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
		return OperandFactory::make($c->mag());
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
		return OperandFactory::make($c->arg());
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

class RealPart extends UnaryOperator implements UnaryComplex
{
	public function scalar(Scalar $s)
	{
		return $s();
	}
	public function complex(Complex $c)
	{
		return $c->real;
	}
}

class ImagPart extends UnaryOperator implements UnaryComplex
{
	public function scalar(Scalar $s)
	{
		return 0;
	}
	public function complex(Complex $c)
	{
		return $c->imag;
	}
}
