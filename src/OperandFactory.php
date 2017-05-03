<?php

namespace App;

class OperandFactory
{
	protected static $valid_operands = [
		'pi'=>'Pi',
		'e'=>'Exp',
		'i'=>'Complex',
		'nan'=>'Nan',
		'+inf'=>'PosInf',
		'-inf'=>'NegInf',
	];
	public static function make($string)
	{
		if( ! static::isValid( $string ) )
			throw new Exception("Invalid operand: ".$string);

		if( is_numeric( $string ) )
			$name = "scalar";
		else
			$name = static::$valid_operands[ $string ];
		$class = "App\\Operands\\".ucfirst($name);
		return new $class($string);
	}

	public static function isValid($string)
	{
		return is_numeric($string) || in_array( $string, array_keys( static::$valid_operands ) );
	}
}
