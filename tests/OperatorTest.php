<?php

use PHPUnit\Framework\TestCase;
use App\Operator;

class OperatorTest extends TestCase
{
	public function testCreateNewOperator()
	{
		$operator = new Operator('+');
		$this->assertEquals('+', $operator);
	}
}
