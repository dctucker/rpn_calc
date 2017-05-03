<?php

namespace App;

class Stack
{
	protected $stack = [];

	public function push($input)
	{
		return array_push( $this->stack, $input );
	}

	public function pop($n = 1)
	{
		for( $i = 0; $i < $n; $i++ )
			yield array_pop( $this->stack );
	}

	public function all()
	{
		return $this->stack;
	}

	public function peek()
	{
		return end( $this->stack );
	}

	public function __toString()
	{
		return implode(' ', $this->stack);
	}
}
