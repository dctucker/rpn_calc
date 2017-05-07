<?php

namespace App\Operands;

use App\Operand;
use App\Operator;

use App\Notations\Decimal;
use App\Notations\Octal;
use App\Notations\Hexadecimal;
use App\Notations\Binary;
use App\Notations\Alphabetic;
use App\Notations\Degrees;

abstract class Scalar extends Operand
{
	/**
	 * @return the primitive scalar data
	 */
	public function getValue()
	{
		return $this->symbol * 1;
	}

	/**
	 * @return string + or -
	 */
	public function sign()
	{
		return $this->getValue() > 0 ? '+' : '-';
	}

	public function operate(Operator $op, $other = null)
	{
		if( $other instanceof Complex )
			$ret = $op->scalarComplex( $this, $other );

		if( $op->num_operands == 1 )
			$ret = $op->scalar( $this );
		elseif( $other instanceof Scalar )
		{
			$ret = $op->scalar( $this, $other );
		}

		if( $ret instanceof Operand )
			return $ret;

		$scalar = new static( $ret );
		$scalar->setValue($ret);
		return $scalar;
	}
}

abstract class BaseScalar extends Scalar
{
	public function getValue()
	{
		$raw_part = $this->symbol; //substr( $this->symbol, strlen(static::$prefix) );
		return base_convert( $raw_part, static::$base, 10 ) * 1;
	}

	public function setValue($value)
	{
		$this->symbol = $this->baseSymbol( $value );
	}
}

class DecScalar extends Scalar     { use Decimal; }
class OctScalar extends BaseScalar { use Octal; }
class HexScalar extends BaseScalar { use Hexadecimal; }
class BinScalar extends BaseScalar { use Binary; }

class DegScalar extends Scalar
{
	use Degrees;
	public function getValue()
	{
		$raw_part = str_replace('deg','',$this->symbol); //substr( $this->symbol, strlen(static::$prefix) );
		return deg2rad( $raw_part );
	}
	public function setValue($value)
	{
		$this->symbol = $this->degSymbol( $value );
	}
}


abstract class Constant extends Scalar
{
	use Alphabetic;
	public function operate(Operator $op, $other = null)
	{
		$doppelganger = new DecScalar( $this->getValue() );
		return $doppelganger->operate($op, $other);
	}
}

class Pi     extends Constant { public function getValue() { return M_PI; } }
class Exp    extends Constant { public function getValue() { return M_E; } }
class Nan    extends Constant { public function getValue() { return NAN; } }
class PosInf extends Constant { public function getValue() { return INF; } }
class NegInf extends Constant { public function getValue() { return -INF; } }

class Complex extends Operand
{
	use \App\Notations\Complex;

	public $real;
	public $imag;

	/**
	 * initialize this Complex real and imag components
	 * @param $real mixed
	 * @param $imag Scalar or double - defaults to one
	 */
	public function __construct($real, $imag=0)
	{
		if( is_array( $real ) )
		{
			$this->real = new DecScalar( $real[0] );
			$this->imag = new DecScalar( $real[1] );
		}
		elseif( is_string( $real ) )
		{
			$this->setValue( $real );
		}
		elseif( is_numeric( $real ) && is_numeric( $imag ) )
		{
			$this->real = new DecScalar( $real );
			$this->imag = new DecScalar( $imag );
		}
		elseif( $real instanceof Scalar && $imag instanceof Scalar )
		{
			$this->real = $real;
			$this->imag = $imag;
		}
	}

	/**
	 * @return array of the primitive real and imaginary values
	 */
	public function getValue()
	{
		return [ $this->real(), $this->imag() ];
	}

	public function setValue($string)
	{
		$matches = static::regex($string);
		$real = $matches[2] ?? 0;
		$imag = str_replace('+','', ( $matches[3] ?? '' ) . ($matches[4] ?? 1));
		$this->real = new DecScalar( $real * 1 );
		$this->imag = new DecScalar( $imag * 1 );
	}

	public function __toString()
	{
		if( $this->real() == 0 )
		{
			if( $this->imag() == 1 )
				return "i";
			elseif( $this->imag() == -1 )
				return "-i";
			else
				return $this->imag."i";
		}
	
		$str = "".$this->real;
		if( $this->imag == '1' )
			$str .= "+i";
		elseif( $this->imag == '-1' )
			$str .= "-i";
		elseif( $this->imag() != 0 )
			$str .= ($this->imag() >= 0 ? '+' : '').$this->imag."i";
		return $str;
	}

	/**
	 * @return double magnitude of this complex vector
	 */
	public function mag()
	{
		return sqrt( pow( $this->real(), 2 ) + pow( $this->imag(), 2 ) );
	}

	/**
	 * @return double argument (phase) of this complex vector
	 */
	public function arg()
	{
		return atan2( $this->imag(), $this->real() );
	}

	public function operate( Operator $op, $other = null )
	{
		$complex = false;
		if( $op->num_operands == 1 )
		{
			if( $op->implements('UnaryComplex') )
			{
				$complex = $op->complex( $this );
			}
		}
		elseif( $other instanceof Complex )
		{
			if( $op->implements('BinaryComplex') )
			{
				$complex = $op->complex( $this, $other );
			}
		}
		elseif( $other instanceof Scalar )
		{
			if( $op->implements('BinaryComplexScalar') )
			{
				$complex = $op->complexScalar( $this, $other );
			}
		}

		if( ! $complex )
			return false;

		if( $complex instanceof Operand )
			return $complex;

		return new Complex( new DecScalar($complex[0]), new DecScalar($complex[1]) );
	}
}

class PolarComplex extends Complex
{
	use \App\Notations\PolarComplex;
	public function __construct($mag, $arg=null)
	{
		if( is_string( $mag ) && $arg === null )
		{
			$this->setValue($mag);
		}
		else
		{
			$this->setMagArg( $mag, $arg );
		}
	}

	public function setValue($string)
	{
		$matches = static::regex($string);
		$this->setMagArg( $matches[1], deg2rad($matches[2]) );
	}

	public function setMagArg($mag, $arg)
	{
		$real = $mag * cos( $arg );
		$imag = $mag * sin( $arg );
		if( abs($real) < 1e-10 ) $real = 0;
		if( abs($imag) < 1e-10 ) $imag = 0;
		$this->real = new DecScalar($real);
		$this->imag = new DecScalar($imag);
	}

	public function __toString()
	{
		return $this->mag()."cis".rad2deg($this->arg())."deg";
	}
}
