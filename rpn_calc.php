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
elseif( $arg == '-h' )
{
	echo "Operators: ".App\OperatorFactory::reference()."\n";
	echo "Operands:  ".App\OperandFactory::reference()."\n";
	return;
}

if( empty( $arg ) )
	return usage();

$parser->parse( $arg );
echo $calc->display();
echo "\n";
