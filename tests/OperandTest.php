<?php

use PHPUnit\Framework\TestCase;
use App\Operand;

class OperandTest extends TestCase
{
	public function testCreateOperand()
	{
		$operand = new Operand('123');
		$this->assertEquals('123', $operand);
	}
}
