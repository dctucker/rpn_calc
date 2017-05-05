<?php

namespace App;

class Parser
{
	public $verbose = false;
	public function __construct(Calculator $calc)
	{
		$this->calculator = $calc;
	}

	/**
	 * loop through given tokens and apply them to the Calculator
	 * @param $tokens array of string
	 */
	public function parse($tokens)
	{
		if( is_string( $tokens ) )
			$tokens = explode(' ', $tokens);

		//echo implode(' ', $tokens)."\n";

		foreach( $tokens as $token )
		{
			$token = trim($token);
			if( strlen( $token ) == 0 )
				continue;
			$sym = $this->resolveSymbol( trim($token) );
			if( $sym instanceof Symbol )
				$this->calculator->push($sym);
			else
				echo "Warning: symbol not recognized: ".$token."\n";
			if( $this->calculator->warning )
				echo $this->calculator->warning."\n";
			if( $this->verbose )
			{
				echo "$sym\t";
				echo "STACK: ".$this->calculator->stack;
				echo "\n";
			}
		}
	}

	/**
	 * validate the given token and run it through the appropriate factory
	 * @param $string string
	 * @return Symbol or void
	 */
	public function resolveSymbol($string)
	{
		if(OperandFactory::isValid ($string)) return OperandFactory::make($string);
		if(OperatorFactory::isValid($string)) return OperatorFactory::make($string);
	}
}
