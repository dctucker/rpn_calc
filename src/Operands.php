<?php

namespace App\Operands;

use App\Operand;
use App\Operator;

class Scalar extends Operand
{
	public function getValue()
	{
		return $this->symbol;
	}

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

		return new Scalar( $ret );
	}
}
class Pi extends Scalar
{
	public function getValue()
	{
		return M_PI;
	}
}
class Exp extends Scalar
{
	public function getValue()
	{
		return M_E;
	}
}
class Nan extends Scalar
{
	public function getValue()
	{
		return NAN;
	}
}
class PosInf extends Scalar
{
	public function getValue()
	{
		return INF;
	}
}
class NegInf extends Scalar
{
	public function getValue()
	{
		return -INF;
	}
}

class Complex extends Operand
{
	public static $default_format = "rectangular";
	public $format;
	public $real;
	public $imag;

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

	public function polar()
	{
		return $this->mag()."exp".rad2deg($this->arg())."deg";
	}

	public function mag()
	{
		return sqrt( pow( $this->real(), 2 ) + pow( $this->imag(), 2 ) );
	}

	public function arg()
	{
		return atan2( $this->imag(), $this->real() );
	}

	public function operate( Operator $op, $other = null )
	{
		if( $op->num_operands == 1 )
		{
			$complex = $op->complex( $this );
		}
		elseif( $other instanceof Complex )
		{
			$complex = $op->complex( $this, $other );
		}
		elseif( $other instanceof Scalar )
		{
			$complex = $op->scale( $this, $other );
		}
		else
		{
			throw new \Exception("unrecognized operand");
		}

		if( $complex instanceof Operand )
			return $complex;

		return new Complex( new Scalar($complex[0]), new Scalar($complex[1]) );
	}
}
