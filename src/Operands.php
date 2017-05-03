<?php

namespace App\Operands;

use App\Operand;
use App\Operator;

class Scalar extends Operand
{
	public $value;
	public function __construct($value)
	{
		$this->value = $value;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function sign()
	{
		return $this->value > 0 ? '+' : '-';
	}

	public function mag()
	{
		return abs($this->value);
	}

	public function __toString()
	{
		return "".$this->value;
	}

	public function operate(Operator $op, $other)
	{
		if( $other instanceof Complex )
			return $other->operate( $op, $this );

		assert( $other instanceof Scalar );
		$this->value = $op->scalars( $this, $other );
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

	public function __toString()
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
	
		$str = $this->real;
		if( $this->imag->value == 1 )
			$str .= "+i";
		elseif( $this->imag->value != 0 )
			$str .= $this->imag->sign().$this->imag->mag()."i";
		return $str;
	}

	public function operate( Operator $op, $other )
	{
		if( $other instanceof Complex )
		{
			$complex = $op->complex( $this, $other );
			$this->real->value = $complex[0];
			$this->imag->value = $complex[1];
		}
		elseif( $other instanceof Scalar )
		{
			// @TODO
			$complex = $op->scale( $this, $other );
		}

		return $this;
	}
}
