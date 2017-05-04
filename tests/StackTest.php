<?php

use PHPUnit\Framework\TestCase;
use App\GeneratorStack as Stack;

class StackTest extends TestCase
{
	public function setUp()
	{
		$this->stack = new Stack;
	}

	public function testCanPushAndPop()
	{
		$a = 'A';
		$this->stack->push($a);
		$b = $this->stack->pop()->current();
		$this->assertEquals($b, $a);
	}
}
