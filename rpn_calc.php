#!/usr/bin/env php
<?php

require_once('autoload.php');

use App\Calculator;
use App\Stack;

class Parser
{
	public $verbose = false;
	public function __construct(Calculator $calc)
	{
		$this->calculator = $calc;
	}

	public function parse($tokens)
	{
		if( is_string( $tokens ) )
			$tokens = explode(' ', $tokens);

		foreach( $tokens as $token )
		{
			$this->calculator->push( trim($token) );
			if( $this->verbose )
			{
				echo "STACK: ".$this->calculator->stack."\n";
			}
		}
	}
}

function usage()
{
	die("Usage: rpn_calc.php [-v] \"<operand> <operand> <operator> ...\"\n");
}


$calc = new Calculator( new Stack );
$parser = new Parser( $calc );

$cmd = array_shift( $argv );
$arg = array_shift( $argv );
if( $arg == '-v' )
{
	$parser->verbose = true;
	$arg = array_shift( $argv );
}

if( empty( $arg ) )
{
	return usage();
}

$parser->parse( $arg );
echo $calc->getValue();
echo "\n";
