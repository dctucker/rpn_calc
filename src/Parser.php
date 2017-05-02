<?php

namespace App;

class Parser
{
	public $verbose = false;
	public function __construct(Calculator $calc)
	{
		$this->calculator = $calc;
	}

	public function parse($tokens)
	{
		if( is_string( $tokens ) )
			$tokens = explode(' ', $tokens);

		foreach( $tokens as $token )
		{
			$this->calculator->push( trim($token) );
			if( $this->verbose )
			{
				echo "STACK: ".$this->calculator->stack."\n";
			}
		}
	}
}
