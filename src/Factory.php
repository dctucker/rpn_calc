<?php

namespace App;

abstract class SymbolFactory
{
	protected static $valids;
	protected static $namespace;

	public static function __callStatic($name, $args)
	{
		return static::make($name);
	}

	public static function make($string)
	{
		if( ! static::isValid( $string ) )
			throw new \Exception("Invalid operator: ".$string);

		$name = static::lookupClassname($string);
		$class = static::$namespace."\\".$name;
		return new $class($string);
	}

	public static function lookupClassname($string)
	{
		return ucfirst( static::$valids[ $string ] );
	}

	public static function isValid($string)
	{
		return in_array( $string, array_keys(static::$valids) );
	}

	public static function reference()
	{
		return implode(' ', array_keys( static::$valids ));
	}
}

class OperatorFactory extends SymbolFactory
{
	protected static $namespace = "App\Operators";
	protected static $valids = [
		'+'=>'plus',
		'-'=>'minus',
		'*'=>'times',
		'/'=>'divide',
		'1/x'=>'reciprocal',
		'^'=>'power',
		'sqrt'=>'sqrt',
		'ln'=>'ln',
		'nthlog'=>'nthLog',
		'sin'=>'sin',
		'cos'=>'cos',
		'tan'=>'tan',
		'mag'=>'mag',
		'arg'=>'arg',
		'conj'=>'conj',
	];
}

class OperandFactory extends SymbolFactory
{
	protected static $namespace = "App\Operands";
	protected static $valids = [
		'pi'=>'Pi',
		'e'=>'Exp',
		'i'=>'Complex',
		'nan'=>'Nan',
		'+inf'=>'PosInf',
		'-inf'=>'NegInf',
	];

	public static function isValid($string)
	{
		return is_numeric($string) || parent::isValid($string);
	}

	public static function lookupClassname($string)
	{
		if( is_numeric( $string ) )
			return "Scalar";
		return parent::lookupClassname($string);
	}
}

