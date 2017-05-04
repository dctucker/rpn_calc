<?php

namespace App;

class Stack
{
	protected $stack = [];

	public function push($input)
	{
		//print_r( $input ); //debug
		if( ! $input )
			return false;
		if( is_array($input) || $input instanceof \Generator )
		{
			$success = 0;
			foreach( $input as $inp )
				$success += array_push( $this->stack, $inp );
			return $success;
		}
		return array_push( $this->stack, $input );
	}

	public function pop($n = 1)
	{
		for( $i = 0; $i < $n; $i++ )
			yield array_pop( $this->stack );
	}

	public function size()
	{
		return count( $this->stack );
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

class NonCommutativeStack extends Stack
{
	public function pop($n = 1)
	{
		yield from array_reverse( iterator_to_array( parent::pop($n) ) );
	}
}
