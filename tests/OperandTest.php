<?php

use PHPUnit\Framework\TestCase;
use App\OperandFactory;

class OperandTest extends TestCase
{
	public function testCreateOperand()
	{
		$operand = OperandFactory::make('123');
		$this->assertEquals('123', $operand);
	}
}
