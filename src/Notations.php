<?php

namespace App\Notations;

interface Notation
{
	public static function regex();
}

trait Regex
{
	/**
	 * @param $string string to pattern-match, or null to return pattern
	 * @return string regex pattern or boolean indicating string matched
	 */
	public static function regex($string=null)
	{
		$pattern = static::pattern();
		if( $string === null ) return $pattern;
		$matches = [];
		if( preg_match($pattern, $string, $matches) )
			return $matches;
	}
	/**
	 * @return string of regular expression to use
	 */
	public static function pattern()
	{
		return "/^.*$/";
	}
}

trait Complex
{
	use Regex;
	public static function pattern()
	{
		return '/^((-?[0-9.]+)([+-]))?([0-9.]+)?i$/';
	}
}

trait Degrees
{
	use Regex;
	public static function pattern()
	{
		return '/^(-?[0-9.]+)deg$/';
	}
	public function degSymbol($number)
	{
		return rad2deg($number)."deg";
	}
}

trait Alphabetic
{
	use Regex;
	public static function pattern()
	{
		return "/^[+-]?[^0-9].*$/";
	}
}

trait Base
{
	use Regex;
	/**
	 * @param $integer numeric
	 * @return string token representing the given integer in this base
	 */
	public function baseSymbol($integer)
	{
		return static::$prefix.base_convert( $integer, 10, static::$base );
	}

	public static function pattern()
	{
		$chars = "0-";
		if( static::$base > 10 )
		{
			$chars .= "9A-".chr(54+static::$base);
			$chars .=  "a-".chr(86+static::$base);
		}
		else
		{
			$chars .= static::$base - 1;
		}
		return "/^".static::$prefix."[$chars]+$/";
	}
}

trait Decimal
{
	use Base;
	static $prefix = "";
	static $base = 10;
	public static function pattern()
	{
		return '/^(-?[0-9.]+)$/';
	}
}

trait Octal
{
	use Base;
	static $prefix = "o";
	static $base = 8;
}

trait Hexadecimal
{
	use Base;
	static $prefix = "0x";
	static $base = 16;
}

trait Binary
{
	use Base;
	static $prefix = "b";
	static $base = 2;
}
