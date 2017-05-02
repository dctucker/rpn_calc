<?php

namespace App;

class Stack
{
	protected $stack = [];

	public function push($input)
	{
		return array_unshift( $this->stack, $input );
	}

	public function pop()
	{
		return array_shift( $this->stack );
	}

	public function all()
	{
		return $this->stack;
	}

	public function peek()
	{
		$ret = $this->pop();
		$this->push($ret);
		return $ret;
	}

	public function __toString()
	{
		return implode(' ', $this->stack);
	}
}
