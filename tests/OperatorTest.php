<?php

use PHPUnit\Framework\TestCase;
use App\OperatorFactory;

class OperatorTest extends TestCase
{
	public function testCreateNewOperator()
	{
		$operator = OperatorFactory::make('+');
		$this->assertEquals('+', $operator);
	}
}
