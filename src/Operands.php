<?php

namespace App\Operands;

use App\Operand;
use App\Operator;

class Scalar extends Operand
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
			return $other->operate( $op, $this );

		if( $op->num_operands == 1 )
			$ret = $op->scalar( $this );
		else
		{
			assert( $other instanceof Scalar );
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
		$this->symbol = static::$prefix.base_convert( $value, 10, static::$base );
	}
}

class OctalScalar  extends BaseScalar { use \App\Bases\Octal; }
class HexScalar    extends BaseScalar { use \App\Bases\Hexidecimal; }
class BinaryScalar extends BaseScalar { use \App\Bases\Binary; }

class Pi     extends Scalar { public function getValue() { return M_PI; } }
class Exp    extends Scalar { public function getValue() { return M_E; } }
class Nan    extends Scalar { public function getValue() { return NAN; } }
class PosInf extends Scalar { public function getValue() { return INF; } }
class NegInf extends Scalar { public function getValue() { return -INF; } }

class Complex extends Operand
{
	public static $default_format = "rectangular";
	public $format;
	public $real;
	public $imag;

	/**
	 * initialize this Complex real and imag components, and default format
	 * @param $real Scalar or double - defaults to zero
	 * @param $imag Scalar or double - defaults to one
	 */
	public function __construct($real, $imag=1)
	{
		if( $real instanceof Scalar && $imag instanceof Scalar )
		{
			$this->real = $real;
			$this->imag = $imag;
		}
		else
		{
			$this->real = new Scalar(0);
			$this->imag = new Scalar($imag);
		}
		$this->format = static::$default_format;
	}

	/**
	 * @return array of the primitive real and imaginary values
	 */
	public function getValue()
	{
		return [ $this->real(), $this->imag() ];
	}

	public function __toString()
	{
		if( $this->format == 'rectangular' )
			return $this->rectangular();
		elseif( $this->format == 'polar' )
			return $this->polar();

		return $this->real .','. $this->imag . 'i';
	}

	/**
	 * @return string e.g. 4+5i
	 */
	public function rectangular()
	{
		if( $this->real == '0' )
		{
			if( $this->imag == '1' )
				return "i";
			elseif( $this->imag == '-1' )
				return "-i";
			else
				return $this->imag."i";
		}
	
		$str = "".$this->real;
		if( $this->imag == '1' )
			$str .= "+i";
		elseif( $this->imag != '0' )
			$str .= $this->imag->sign().abs( $this->imag() )."i";
		return $str;
	}

	/**
	 * polar representation in degrees of this complex vector
	 * @return string e.g. 3.6055exp45deg
	 */
	public function polar()
	{
		return $this->mag()."exp".rad2deg($this->arg())."deg";
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
				$complex = $op->scale( $this, $other );
			}
		}

		if( ! $complex )
			return false;

		if( $complex instanceof Operand )
			return $complex;

		return new Complex( new Scalar($complex[0]), new Scalar($complex[1]) );
	}
}
