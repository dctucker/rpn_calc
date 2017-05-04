<?php

use PHPUnit\Framework\TestCase;
use App\OperandFactory;

class OperandTest extends TestCase
{
	public function testCreateOperand()
	{
		$operand = OperandFactory::make('123');
		$this->assertEquals('123', $operand);

		$operand = OperandFactory::make('pi');
		$this->assertEquals(M_PI, $operand());

		$operand = OperandFactory::make('e');
		$this->assertEquals(M_E, $operand());

		$operand = OperandFactory::make('nan');
		$this->assertNan($operand());

		$operand = OperandFactory::make('+inf');
		$this->assertEquals(INF, $operand());

		$operand = OperandFactory::make('-inf');
		$this->assertEquals(-INF, $operand());
	}
}
