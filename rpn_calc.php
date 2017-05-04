#!/usr/bin/env php
<?php

require_once('autoload.php');

use App\Parser;
use App\Calculator;
use App\NonCommutativeStack as Stack;

function usage()
{
	die("Usage: rpn_calc.php [-v] \"<operand> <operand> <operator> ...\"\n");
}

$calc = new Calculator( new Stack );
$parser = new Parser( $calc );

$cmd = array_shift( $argv );
$arg = array_shift( $argv );
if( strpos($arg, 'p') !== false )
{
	$calc->setComplexFormat('polar');
}
if( strpos($arg, 'h') !== false )
{
	echo "Operators: ".App\OperatorFactory::reference()."\n";
	echo "Operands:  ".App\OperandFactory::reference()."\n";
	return;
}
if( strpos($arg, 'v') !== false )
{
	$parser->verbose = true;
	$arg = array_shift( $argv );
}

if( empty( $arg ) )
	return usage();

$parser->parse( $arg );
echo $calc->display();
echo "\n";
