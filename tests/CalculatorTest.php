<?php

use PHPUnit\Framework\TestCase;
use App\Calculator;
use App\Parser;
use App\NonCommutativeStack as Stack;

class S extends App\Symbol
{
}

class CalculatorTest extends TestCase
{
	public function testCreateCalculator()
	{
		$stack = new Stack;
		$calc = new Calculator($stack);
		$this->assertEmpty( $calc->stack->all() );
	}

	public function testCalculation()
	{
		$calc = new Calculator( new Stack );
		$calc->push( new App\Operands\DecScalar("123") );
		$calc->push( new App\Operands\DecScalar("456") );
		$calc->push( new App\Operators\Times("*") );
		$this->assertEquals( 123 * 456, $calc->display() );
		$this->assertEquals( 56088, $calc->display() );

		$calc->setComplexFormat('polar');
		$calc->setComplexFormat('rectangular');
		$calc->push(new S(''));
		$calc->push( new App\Operands\Complex("2+3i") );
		$calc->push( new App\Operands\Complex("2+3i") );
		$calc->push( new App\Operators\Power("^") );
	}

	public function testParser()
	{
		$parser = new Parser( new Calculator( new Stack ) );
		$parser->verbose = true;
		$this->assertNotEmpty( $parser );
		$parser->parse("+");
		$parser->parse("");
		$parser->parse("something");
	}
}

