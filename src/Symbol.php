<?php

namespace App;

abstract class Symbol
{
	public $symbol;
	public function __construct($symbol)
	{
		$this->symbol = $symbol;
	}

	public function __toString()
	{
		return "".$this->symbol;
	}
}

abstract class Operator extends Symbol
{
	public $identity;
	public $num_operands;

	public function __toString()
	{
		return $this->symbol;
	}

	public function generate($a)
	{
		if( is_array( $a ) && count( $a ) == 1 )
			$a = reset($a);
		if( is_array( $a ) )
			yield from $a;
		elseif( $a instanceof \Generator )
			yield from $a;
		else
			yield $a;
	}

}

abstract class Operand extends Symbol
{
	public abstract function operate( Operator $op, $other );

	public function __invoke()
	{
		return $this->getValue();
	}

	public function __call($name, $args)
	{
		if( property_exists( $this, $name ) )
			return $this->$name->getValue();
		else
			throw new \Exception("Attribute not found: $string");
	}

	public abstract function getValue();
}
