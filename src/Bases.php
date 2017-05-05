<?php

namespace App\Bases;

trait Decimal
{
	static $prefix = "";
	static $base = 10;
}

trait Octal
{
	static $prefix = "o";
	static $base = 8;
}

trait Hexidecimal
{
	static $prefix = "0x";
	static $base = 16;
}

trait Binary
{
	static $prefix = "b";
	static $base = 2;
}


