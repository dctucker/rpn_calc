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

		$this->assertNotEmpty( OperatorFactory::make('dump')(O(1)) );
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
		$this->assertEquals( O('-2i'), OperatorFactory::make('/')( O( 2), O('i') ) );
		$this->assertEquals( O('i'), new App\Operands\Complex([0,1]) );
		$this->assertEquals( O('0+0i'), new App\Operands\Complex(0) );
	}

	public function testComplexScalarOperators()
	{
		//$this->assertEquals( O("1cis0deg"), OperatorFactory::make('^')( O('1'), O('i') ) );
		//$this->assertEquals( O("2+11i"), OperatorFactory::make('^')( O("2+i"), O(3) ) );
		$this->assertEquals( O("6+3i") , OperatorFactory::make('*')( O("2+i"), O(3) ) );
		$this->assertEquals( O("6+i")  , OperatorFactory::make('+')( O("2+i"), O(4) ) );

		$ln = OperatorFactory::make('ln')( O("100+100i") );
		$log= OperatorFactory::make('nthlog')( O("100+100i"), O('e') );
		$this->assertEquals( $log , $ln );

		$ln = OperatorFactory::make('ln')( O("100") );
		$log= OperatorFactory::make('nthlog')( O("100"), O('e') );
		$this->assertEquals( $log , $ln );
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
		$this->assertNan(              OperatorFactory::make('mod')->scalar(O( 5),O(0)));
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

		$deg180 = O('180deg');
		$this->assertEquals(     M_PI, OperatorFactory::make('deg')->scalar($deg180)());
		$this->assertEquals(     M_PI, OperatorFactory::make('rad')->scalar($deg180)());

		$this->assertEquals( O("-26"), OperatorFactory::make('not')( O("25") ) );
		$this->assertEquals( O("0xfe"), OperatorFactory::make('not')( O("0x01") ) );
	}

	public function testComplexMethods()
	{
		$this->assertEquals([0, 5], OperatorFactory::make('+')->complex(O("2i"),O("3i")));
		$this->assertEquals([0,-1], OperatorFactory::make('-')->complex(O("2i"),O("3i")));
		$this->assertEquals([-6,0], OperatorFactory::make('*')->complex(O("2i"),O("3i")));
		$this->assertEquals([2/3,0], OperatorFactory::make('/')->complex(O("2i"),O("3i")));

		$ret = OperatorFactory::make('/')->complex(O("2i"),O("0i"));
		$this->assertNotEmpty( $ret );
		$this->assertNan( $ret[0] );
		$this->assertNan( $ret[1] );

		$ret = OperatorFactory::make('1/x')->complex(O("0i"));
		$this->assertNotEmpty( $ret );
		$this->assertNan( $ret[0] );
		$this->assertNan( $ret[1] );

		$this->assertEquals( O('0.5i'), OperatorFactory::make('/')(O("i"),O("2")) );
		$this->assertEquals( O('-0.6366197723675813430755350534900574481378385829618257949i'), OperatorFactory::make('nthlog')(O('e'),O('i')) );

		$this->assertEquals( O('-1+0i')(), OperatorFactory::make('^')(O('i'),O('2'))() );
		$ipi = OperatorFactory::make('*')(O('i'),O('pi'));
		$this->assertEquals( O('-1+0i')(), OperatorFactory::make('^')(O('e'),$ipi)() );
	}

	public function testBaseConversion()
	{
		$this->assertEquals("0xff", OperatorFactory::make('hex')->scalar(O(255)));
		$this->assertEquals("o377", OperatorFactory::make('oct')->scalar(O(255)));
		$this->assertEquals("b11111111", OperatorFactory::make('bin')->scalar(O(255)));
		$this->assertEquals("255", OperatorFactory::make('dec')->scalar(O(255)));
	}


	public function generator()
	{
		yield 'A';
	}

	public function testSymbolGeneratorHelper()
	{
		$operator = OperatorFactory::make('+');
		$generator = $this->generator();
		$ret = $operator->generate( $generator );
		$this->assertNotEmpty( $ret );
		$ret = $operator->generate( 'A' );
		$this->assertNotEmpty( $ret );
	}

	public function testCallInvalidOperation()
	{
		$this->expectException(\Exception::class);
		$operation = OperatorFactory::make('+');
		$operation->re();
	}

	public function testStackOperators()
	{
		$operator = OperatorFactory::make('push');
		$ret = $operator("A");
		$this->assertNotEmpty($ret);

		$this->assertNotEmpty( OperatorFactory::make('dump')( OperandFactory::make('i') ) );
	}

	public function testInvalidOperator()
	{
		$this->expectException(\Exception::class);
		$this->assertEmpty( OperatorFactory::make('something') );
	}

	public function testCommutativity()
	{
		$plus = OperatorFactory::make('+');
		$this->assertEquals( $plus(O(2),O('i')), $plus(O('i'),O(2)) );

		$plus = OperatorFactory::make('*');
		$this->assertEquals( $plus(O(2),O('i')), $plus(O('i'),O(2)) );

	}
}
