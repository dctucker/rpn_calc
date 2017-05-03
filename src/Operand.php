<?php

namespace App;

abstract class Operand
{
	public abstract function __toString();
	public abstract function operate( Operator $op, $other );
}
