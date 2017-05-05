<?php

use PHPUnit\Framework\TestCase;
use App\OperatorFactory;
use App\OperandFactory as OperandFactory;

function O($number){
	return OperandFactory::make($number);
}

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
		$ret = $times( O(123), O(456) );
		$this->assertEquals( 123*456, $ret() );

		$minus = OperatorFactory::make('-');
		$ret = $minus( O(123), O(456) );
		$this->assertEquals( 123-456, $ret() );
	}

	/**
	 * instantiate and invoke every operator with no input, expect empty
	 */
	public function testAllOperatorsWithNoInput()
	{
		foreach( OperatorFactory::reference() as $key )
		{
			$operator = OperatorFactory::make( $key );
			$this->assertNotEmpty( $operator );
			$result = ($operator)();
			if( $result instanceof \Generator )
				$result = iterator_to_array( $result );
			$this->assertEmpty( $result );
		}
	}

	public function testScalarMethods()
	{
		$this->assertEquals(  1+2, OperatorFactory::make('+')->scalar(O( 1),O( 2 )));
		$this->assertEquals(  3-4, OperatorFactory::make('-')->scalar(O( 3),O( 4 )));
		$this->assertEquals( -5*8, OperatorFactory::make('*')->scalar(O(-5),O( 8 )));
		$this->assertEquals(  9/7, OperatorFactory::make('/')->scalar(O( 9),O( 7 )));
		$this->assertEquals( 2**7, OperatorFactory::make('^')->scalar(O( 2),O( 7 )));

		$this->assertEquals(     -0.5, OperatorFactory::make('1/x')->scalar(O( -2 )));
		$this->assertEquals( log(200), OperatorFactory::make('ln') ->scalar(O( 200 )));
		$this->assertEquals(        1, OperatorFactory::make('cos')->scalar(O(0)));
		$this->assertEquals(        0, OperatorFactory::make('sin')->scalar(O(0)));
		$this->assertEquals(        0, OperatorFactory::make('tan')->scalar(O(0)));
	}
}
