<?php

namespace App;

use App\Notations\Octal;
use App\Notations\Decimal;
use App\Notations\Hexadecimal;
use App\Notations\Binary;
use App\Notations\Complex;
use App\Notations\Alphabetic;

interface Factory
{
	public static function make($string);
}

abstract class SymbolFactory implements Factory
{
	protected static $valids;
	protected static $namespace;

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
		'int'=>'intval',
		'frac'=>'frac',
		'mod'=>'modulo',
		'round'=>'round',
		'bin'=>'bin',
		'hex'=>'hex',
		'dec'=>'dec',
		'oct'=>'oct',
		'and'=>'bAnd',
		'or'=>'bOr',
		'xor'=>'bXor',
		'not'=>'bNot',
		'shl'=>'bShiftLeft',
		'shr'=>'bShiftRight',
		'sqrt'=>'sqrt',
		'ln'=>'ln',
		'nthlog'=>'nthLog',
		'sin'=>'sin',
		'cos'=>'cos',
		'tan'=>'tan',
		're'=>'realPart',
		'im'=>'imagPart',
		'mag'=>'mag',
		'arg'=>'arg',
		'conj'=>'conj',
		'pop'=>'pop',
		'push'=>'push',
		'swap'=>'swap',
		'dump'=>'dump',
		//'<<'=>'rotateL',
		//'>>'=>'rotateR',
	];
}

class OperandFactory extends SymbolFactory
{
	protected static $namespace = "App\Operands";
	protected static $valids = [
		'pi'=>'Pi',
		'π'=>'Pi',
		'e'=>'Exp',
		'i'=>'Complex',
		'nan'=>'Nan',
		'+inf'=>'PosInf',
		'-inf'=>'NegInf',
	];

	public static function isValid($string)
	{
		return static::isDecimal($string) || static::isOctal($string)
			|| static::isHex($string)     || static::isBinary($string)
			|| static::isComplex($string)
			|| parent::isValid($string);
	}

	public static function lookupClassname($string)
	{
		if(static::isDecimal($string)) return "DecScalar";
		if(static::isBinary ($string)) return "BinScalar";
		if(static::isOctal  ($string)) return "OctScalar";
		if(static::isHex    ($string)) return "HexScalar";
		if(static::isComplex($string)) return "Complex";
		if(static::isAlphabetic($string))
		return parent::lookupClassname($string);
	}

	public static function isHex($string)     { return Hexadecimal::regex($string); }
	public static function isBinary($string)  { return Binary::regex($string); }
	public static function isOctal($string)   { return Octal::regex($string); }
	public static function isDecimal($string) { return Decimal::regex($string); }
	public static function isComplex($string) { return Complex::regex($string); }
	public static function isAlphabetic($string) { return Alphabetic::regex($string); }
}
