<?php

namespace App;

interface Factory
{
	public static function make($string);
}

abstract class SymbolFactory implements Factory
{
	protected static $valids;
	protected static $namespace;

	/**
	 * @TODO
	 */
	public static function __callStatic($name, $args)
	{
		return static::make($name);
	}

	/**
	 * factory method, return object based on specified string
	 * @param $string string identifying which class to load
	 */
	public static function make($string)
	{
		if( ! static::isValid( $string ) )
			throw new \Exception("Invalid operator: ".$string);

		$name = static::lookupClassname($string);
		$class = static::$namespace."\\".$name;
		return new $class($string);
	}

	/**
	 * @param $string string class to lookup
	 * @return base name of the Symbol to instantiate
	 */
	public static function lookupClassname($string)
	{
		return ucfirst( static::$valids[ $string ] );
	}

	/**
	 * validate the given string to see if a Symbol can be made
	 * @param $string string
	 * @return boolean
	 */
	public static function isValid($string)
	{
		return in_array( $string, array_keys(static::$valids) );
	}

	/**
	 * @return string space-separated representation of the valid Symbols for this factory
	 */
	public static function reference()
	{
		return array_keys( static::$valids );
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
		'-x'=>'negative',
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
		'pop'=>'pop',
		'swap'=>'swap',
		//'<<'=>'rotateL',
		//'>>'=>'rotateR',
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

