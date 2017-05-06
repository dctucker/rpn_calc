<?php

namespace App;

abstract class Symbol
{
	public $symbol;
	public $required_interface = "";

	/**
	 * @param $symbol string representation/value of this symbol
	 */
	public function __construct($symbol)
	{
		assert( ! $symbol instanceof Symbol );
		$this->symbol = $symbol;
	}

	/**
	 * check if class implements an interface in the same namespace
	 * assign the checked interface name to $required_interface
	 * @return boolean true if implemented or extended, false otherwise
	 */
	public function implements($string)
	{
		$namespace = get_class($this);
		$names = explode("\\", $namespace);
		array_pop( $names );
		$names[] = $string;
		$interface = implode("\\", $names);
		$this->required_interface = $string;
		return $this instanceof $interface;
	}

	public function __toString()
	{
		return "".$this->symbol;
	}
}

abstract class Operator extends Symbol
{
	public $num_operands;

	/**
	 * an operator's members are invoked when called
	 * syntactical sugar:  $this->sin($x)  <=>  ($this->sin)($x)
	 */
	public function __call($func, $args)
	{
		if( property_exists( $this, $func ) )
			return ($this->$func)(...$args);
		else
			throw new \Exception("Attribute not found: $func");
	}

	/**
	 * helper to turn variable arguments into a Generator
	 * @return Generator
	 */
	public function generate($a)
	{
		//if( is_array( $a ) && count( $a ) == 1 )
		//	$a = reset($a);
		if( is_array( $a ) )
			yield from $a;
		elseif( $a instanceof \Generator )
			yield from $a;
		else
			yield $a;
	}

	/**
	 * take any Operand(s) and apply operator to them
	 * @param $operands iterable of operand(s)
	 * @return Operand
	 */
	public abstract function __invoke(...$operands);
}

abstract class Operand extends Symbol implements \App\Notations\Notation
{
	/**
	 * @param $symbol string representation/value of this symbol
	 */
	public function __construct($symbol)
	{
		assert( ! $symbol instanceof Symbol );
		$this->symbol = $symbol;
	}

	/**
	 * apply operator e.g. this + other
	 * @param $op Operator which operation to apply
	 * @param $other Operand
	 * @return Operand the result of running the operation
	 */
	public abstract function operate( Operator $op, $other );

	/**
	 * an Operand's primitive value should be returned when invoked
	 * syntactical sugar:  $x()  <=> $x->getValue()
	 * @return double primitive value of this Operand
	 */
	public function __invoke()
	{
		return $this->getValue();
	}

	/**
	 * syntactical sugar:  $c->real()  <=>  ($c->real)()
	 * @return primitive value of this Operand's specified property
	 */
	public function __call($name, $args)
	{
		if( property_exists( $this, $name ) )
			return $this->$name->getValue();
		else
			throw new \Exception("Attribute not found: $string");
	}

	/**
	 * @return double the primitive value represented by this Operand
	 */
	public abstract function getValue();

	/**
	 * initialize symbol to given input
	 */
	public function setValue($value)
	{
		$this->symbol = $value;
	}

}
