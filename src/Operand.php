<?php

namespace App\Operands;

use App\Operators\Operator;

abstract class Operand
{
	public abstract function __toString();
	public abstract function operate( Operator $op, $other );
}

