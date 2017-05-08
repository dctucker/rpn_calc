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
		$this->assertEquals('-', $operand->sign());

		$this->assertEquals(255, OperandFactory::make("0xff")());
		$this->assertEquals(255, OperandFactory::make("b11111111")());
		$this->assertEquals(255, OperandFactory::make("o377")());

		$operand = OperandFactory::make("0xff");
		$operand->setValue(0x33);
		$this->assertEquals(0x33, $operand->getValue());

		$operand = OperandFactory::make("0deg");
		$operand->setValue(M_PI);
		$this->assertEquals(M_PI, $operand->getValue());

		$operand = OperandFactory::make('1+2i');
		$this->assertEquals([1,2], $operand->getValue());
		$this->assertEquals("1+2i", "".$operand);
		/*
		$operand->format = "polar";
		$this->assertEquals("2.2360679774998exp63.434948822922deg", "".$operand);
		$operand->format = "";
		$this->assertEquals("1,2i", "".$operand);
		*/

		$operand = OperandFactory::make('1+i');
		$this->assertEquals("1+i", $operand);
		$operand = OperandFactory::make('1-i');
		$this->assertEquals("1-i", $operand);
		$operand = OperandFactory::make('i');
		$this->assertEquals("i", $operand);
		$operand = OperandFactory::make('0-i');
		$this->assertEquals("-i", $operand);
		$operand = OperandFactory::make('3i');
		$this->assertEquals("3i", $operand);

		$this->assertNotEmpty( \App\Notations\Regex::pattern() );
	}

	public function testCallInvalidOperand()
	{
		$this->expectException(\Exception::class);
		$operand = OperandFactory::make('1+2i');
		$operand->re();
	}

	public function testInvalidOperand()
	{
		$this->assertEmpty( OperandFactory::lookupClassName("09sa") );
	}

	public function testPolarComplex()
	{
		$polar = OperandFactory::make('4cis90deg');
		$this->assertEquals( OperandFactory::make('4i')(), $polar() );
		$this->assertEquals( "4cis90deg", $polar );
	}


	public function testBaseBooleanNot()
	{
		$this->assertEquals( O('0xff'), O('0x00')->bnot() );
		$this->assertEquals( O('b1010'), O('b0101')->bnot() );
		$this->assertEquals( O('o03'), O('o74')->bnot() );
	}
}
