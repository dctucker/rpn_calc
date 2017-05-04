<?php

namespace App;

interface Stack
{
	public function push($input);
	public function pop($n);
	public function size();
	public function all();
	public function peek();
	public function __toString();
}

class GeneratorStack implements Stack
{
	protected $stack = [];

	/**
	 * push item(s) onto stack
	 * @param $input mixed can be any object or iterable
	 */
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

	/**
	 * pop n items from the stack
	 * @param $n integer number of items to pop from the stack
	 * @return Generator
	 */
	public function pop($n = 1)
	{
		for( $i = 0; $i < $n; $i++ )
			yield array_pop( $this->stack );
	}

	/**
	 * remove all items from the stack
	 */
	public function clear()
	{
		$this->stack = [];
	}

	/**
	 * @return integer number of items on the stack
	 */
	public function size()
	{
		return count( $this->stack );
	}

	/**
	 * @return all items on the stack
	 */
	public function all()
	{
		return $this->stack;
	}

	/**
	 * @return the item on the top of the stack
	 */
	public function peek()
	{
		return end( $this->stack );
	}

	/**
	 * @return string space-separated items
	 */
	public function __toString()
	{
		return implode(' ', $this->stack);
	}
}

class NonCommutativeStack extends GeneratorStack
{
	/**
	 * useful for applying non-commutative operations
	 * @return Generator items from the stack in reverse order
	 */
	public function pop($n = 1)
	{
		$offset = $this->size() - $n;
		for( $i=0 ; $i < $n; $i++ )
			yield from array_splice( $this->stack, $offset, 1 );
		//yield from array_reverse( iterator_to_array( parent::pop($n) ) );
	}
}
