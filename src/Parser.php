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

		echo implode(' ', $tokens)."\n";

		foreach( $tokens as $token )
		{
			$obj = $this->calculator->push( trim($token) );
			if( $this->verbose )
			{
				echo "$obj\t";
				echo "STACK: ".$this->calculator->stack;
				echo "\n";
			}
		}
	}
}
