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

		$this->assertEquals(255, OperandFactory::make("0xff")());
		$this->assertEquals(255, OperandFactory::make("b11111111")());
		$this->assertEquals(255, OperandFactory::make("o377")());

		$operand = OperandFactory::make("0xff");
		$operand->setValue(0x33);
		$this->assertEquals(0x33, $operand->getValue());


		$operand = OperandFactory::make('1+2i');
		$this->assertEquals([1,2], $operand->getValue());
		$this->assertEquals("1+2i", "".$operand);
		$operand->format = "polar";
		$this->assertEquals("2.2360679774998exp63.434948822922deg", "".$operand);
		$operand->format = "";
		$this->assertEquals("1,2i", "".$operand);
	}
}
