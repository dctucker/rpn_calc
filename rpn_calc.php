#!/usr/bin/env php
<?php

require_once('autoload.php');

use App\Parser;
use App\Calculator;
use App\Stack;

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
	return usage();

$parser->parse( $arg );
echo $calc->getValue();
echo "\n";
