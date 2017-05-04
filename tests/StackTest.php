<?php

use PHPUnit\Framework\TestCase;
use App\GeneratorStack as Stack;
use App\NonCommutativeStack as NCStack;

class StackTest extends TestCase
{
	public function setUp()
	{
		$this->stack = new Stack;
		$this->ncstack = new NCStack;
	}

	public function testCanPushAndPop()
	{
		$a = 'A';
		$this->stack->push($a);
		$b = $this->stack->pop()->current();
		$this->assertEquals($b, $a);
	}

	public function testPopStack()
	{
		$this->ncstack->push('A');
		$this->ncstack->push('B');
		$this->ncstack->push('B');
		$popped = $this->ncstack->pop(2);
		foreach( $popped as $pop )
			$this->assertEquals('B', $pop);

		$popped = $this->ncstack->pop(0);
		foreach( $popped as $pop )
			$this->assertGreaterThan(0, $this->ncstack->size());

	}
}
