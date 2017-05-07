<?php

use PHPUnit\Framework\TestCase;
use App\Calculator;
use App\Parser;
use App\NonCommutativeStack as Stack;
use App\Operator;
use App\Operators\UnaryOperator;
use App\Operators\UnaryComplex;

class S extends App\Symbol
{
}

class Nop extends UnaryOperator implements UnaryComplex
{
	public function __invoke(...$operands)
	{
		return false;
	}

	public function scalar(App\Operands\Scalar $s)
	{
	}

	public function complex(App\Operands\Complex $c)
	{
	}
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

	public function testUnsupportedOperator()
	{
		$stack = new Stack;
		$calc = new Calculator($stack);
		$calc->push( new App\Operands\DecScalar("123") );
		$calc->push( new App\Operands\DecScalar("456") );
		$stack_before = $calc->stack->all();

		$nop = new Nop("NOP");
		$calc->push( $nop );
		$this->assertEquals( $stack_before, $calc->stack->all() );

		$c = new App\Operands\Complex('i');
		$this->assertFalse( $c->operate($nop) );
	}
}

