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

	/**
	 * instantiate and invoke every operator with scalar input, expect number
	public function testAllOperatorsScalarInput()
	{
		$params = [ O(3), O(3) ];
		foreach( OperatorFactory::reference() as $key )
		{
			$operator = OperatorFactory::make( $key );
			$this->assertNotEmpty( $operator );
			$result = $operator->scalar(...$params);
			if( $result instanceof \Generator )
				$result = iterator_to_array( $result );
			if( $result instanceof \App\Operands\Scalar )
				$result = $result();
			print_r( $operator);
			$this->assertGreaterThanOrEqual(-999, $result, $operator);
		}
	}
	 */

	/**
	 * instantiate and invoke every operator with no input, expect empty
	 */
	public function testAllOperatorsComplexInput()
	{
		$params = [ O('i'), O('i') ];
		foreach( OperatorFactory::reference() as $key )
		{
			$operator = OperatorFactory::make( $key );
			if( $operator->implements('UnaryComplex') || $operator->implements('BinaryComplex') )
			{
				$this->assertNotEmpty( $operator );
				$result = $operator->complex(...$params);
				if( $result instanceof \Generator )
					$result = iterator_to_array( $result );
			}
		}
	}


	public function testScalarMethods()
	{
		$this->assertEquals(      1+2, OperatorFactory::make('+')->scalar(O( 1),O( 2 )));
		$this->assertEquals(      3-4, OperatorFactory::make('-')->scalar(O( 3),O( 4 )));
		$this->assertEquals(     -5*8, OperatorFactory::make('*')->scalar(O(-5),O( 8 )));
		$this->assertEquals(      9/7, OperatorFactory::make('/')->scalar(O( 9),O( 7 )));
		$this->assertNan(              OperatorFactory::make('/')->scalar(O( 2),O( 0 )));
		$this->assertEquals(     2**7, OperatorFactory::make('^')->scalar(O( 2),O( 7 )));
		$this->assertEquals(        8, OperatorFactory::make('sqrt')->scalar(O(64)));
		$this->assertEquals(        3, OperatorFactory::make('mod')->scalar(O( 11),O( 4 )));
		$this->assertEquals(        3, OperatorFactory::make('int')->scalar(O( 3.9 )));
		$this->assertEquals(      0.9, OperatorFactory::make('frac')->scalar(O( 3.9 )));
		$this->assertEquals(        1, OperatorFactory::make('round')->scalar(O( 0.9 )));

		$this->assertEquals(     -0.5, OperatorFactory::make('1/x')->scalar(O( -2 )));
		$this->assertNan(              OperatorFactory::make('1/x')->scalar(O( 0 )));
		$this->assertEquals(     -0.5, OperatorFactory::make('-x')->scalar(O( 0.5 )));
		$this->assertEquals( log(200), OperatorFactory::make('ln') ->scalar(O( 200 )));
		$this->assertEquals(        1, OperatorFactory::make('cos')->scalar(O(0)));
		$this->assertEquals(        0, OperatorFactory::make('sin')->scalar(O(0)));
		$this->assertEquals(        0, OperatorFactory::make('tan')->scalar(O(0)));

		$this->assertEquals(        3, OperatorFactory::make('and')->scalar(O(7),O(3)));
		$this->assertEquals(        3, OperatorFactory::make('or') ->scalar(O(1),O(2)));
		$this->assertEquals(        0, OperatorFactory::make('xor')->scalar(O(3),O(3)));
		$this->assertEquals(       -4, OperatorFactory::make('not')->scalar(O(3),O(2)));
		$this->assertEquals(       28, OperatorFactory::make('shl')->scalar(O(7),O(2)));
		$this->assertEquals(        1, OperatorFactory::make('shr')->scalar(O(7),O(2)));

		$sqres = O("0+8i");
		$this->assertEquals( $sqres,   OperatorFactory::make('sqrt')->scalar(O(-64)));

		$this->assertEquals(       20, OperatorFactory::make('mag')->scalar(O(-20)));
		$this->assertEquals(     M_PI, OperatorFactory::make('arg')->scalar(O(-20)));
		$this->assertEquals(      -20, OperatorFactory::make('conj')->scalar(O(-20)));
		$this->assertEquals(       20, OperatorFactory::make('re')->scalar(O(20)));
		$this->assertEquals(        0, OperatorFactory::make('im')->scalar(O(20)));
	}

	public function testComplexMethods()
	{
		$this->assertEquals([0, 5], OperatorFactory::make('+')->complex(O("2i"),O("3i")));
		$this->assertEquals([0,-1], OperatorFactory::make('-')->complex(O("2i"),O("3i")));
		$this->assertEquals([-6,0], OperatorFactory::make('*')->complex(O("2i"),O("3i")));
		$this->assertEquals([2/3,0], OperatorFactory::make('/')->complex(O("2i"),O("3i")));
	}

	public function testBaseConversion()
	{
		$this->assertEquals("0xff", OperatorFactory::make('hex')->scalar(O(255)));
		$this->assertEquals("o377", OperatorFactory::make('oct')->scalar(O(255)));
		$this->assertEquals("b11111111", OperatorFactory::make('bin')->scalar(O(255)));
		$this->assertEquals("255", OperatorFactory::make('dec')->scalar(O(255)));
	}
}
