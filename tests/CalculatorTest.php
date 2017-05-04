<?php

use PHPUnit\Framework\TestCase;
use App\Calculator;
use App\NonCommutativeStack as Stack;

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
		$calc->push("123");
		$calc->push("456");
		$calc->push('*');
		$this->assertEquals( 123 * 456, $calc->display() );
		$this->assertEquals( 56088, $calc->display() );
	}
}

