<?php

namespace App\Bases;

trait Base
{
	/**
	 * @param $integer numeric
	 * @return string token representing the given integer in this base
	 */
	public function baseSymbol($integer)
	{
		return static::$prefix.base_convert( $integer, 10, static::$base );
	}

	/**
	 * @param $string string to pattern-match, or null to return pattern
	 * @return string regex pattern or boolean indicating string matched
	 */
	public static function regex($string=null)
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
		$pattern = "/^".static::$prefix."[$chars]+$/";
		if( is_string( $string ) )
			return preg_match($pattern, $string);
		return $pattern;
	}
}

trait Decimal
{
	use Base;
	static $prefix = "";
	static $base = 10;
}

trait Octal
{
	use Base;
	static $prefix = "o";
	static $base = 8;
}

trait Hexidecimal
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
