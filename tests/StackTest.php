<?php

use PHPUnit\Framework\TestCase;
use App\Stack;

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
		$b = $this->stack->pop();
		$this->assertEquals($b, $a);
	}
}
