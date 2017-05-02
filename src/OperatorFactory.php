<?php

namespace App;

class OperatorFactory
{
	protected static $valid_operators = [
		'+'=>'plus',
		'-'=>'minus',
		'*'=>'times',
		'/'=>'divide',
		'^'=>'power',
	];

	public static function make($string)
	{
		if( ! static::isValid( $string ) )
			throw new Exception("Invalid operator: ".$string);

		$name = static::$valid_operators[ $string ];
		$class = "App\\Operators\\".ucfirst($name)."Op";
		return new $class($string);;
	}

	public static function isValid($string)
	{
		return in_array( $string, array_keys(static::$valid_operators) );
	}
}
