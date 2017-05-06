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
		$this->stack->push("");
		$this->stack->push([""]);
		$a = 'A';
		$this->stack->push($a);
		$this->assertEquals("A", $this->stack->peek());
		$b = $this->stack->pop()->current();
		$this->assertEquals($b, $a);
		$this->stack->clear();
		$this->assertEmpty( $this->stack->all() );
		$this->stack->pop(0);
		$this->stack->pop(2);
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
