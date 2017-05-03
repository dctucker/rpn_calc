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
		if( $op->num_operands == 1 )
			return $op->scalar( $this );

		if( $other instanceof Complex )
			return $other->operate( $op, $this );

		assert( $other instanceof Scalar );
		$this->symbol = $op->scalar( $this, $other );
		return $this;
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
	public $real;
	public $imag;

	public function __construct($string)
	{
		$this->real = new Scalar(0);
		$this->imag = new Scalar(1);
	}

	public function getValue()
	{
		return [ $this->real->getValue(), $this->imag->getValue() ];
	}

	public function __toString()
	{
		//return $this->real .','. $this->imag . 'i';

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
			$str .= $this->imag->sign().abs( $this->imag->getValue() )."i";
		return $str;
	}

	public function mag()
	{
		return sqrt( pow( $this->real->getValue(), 2 ) + pow( $this->imag->getValue(), 2 ) );
	}

	public function arg()
	{
		return atan2( $this->imag->getValue(), $this->real->getValue() );
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

		$this->real->symbol = $complex[0];
		$this->imag->symbol = $complex[1];

		if( $this->imag->getValue() == 0 )
			return $this->real;

		return $this;
	}
}
