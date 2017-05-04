<?php

use PHPUnit\Framework\TestCase;
use App\OperatorFactory;
use App\OperandFactory as OperandFactory;

class OperatorTest extends TestCase
{
	public function testCreateNewOperator()
	{
		$operator = OperatorFactory::make('+');
		$this->assertEquals('+', $operator);

	}

	public function testOperators()
	{
		$times = OperatorFactory::make('*');
		function O($number){
			return OperandFactory::make($number);
		}
		$ret = $times( O(123), O(456) );
		$this->assertEquals( 123*456, $ret() );

		$minus = OperatorFactory::make('-');
		$ret = $minus( O(123), O(456) );
		$this->assertEquals( 123-456, $ret() );
	}
}
