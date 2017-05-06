<html charset='utf8'>
<head>
<title>RPN Calc</title>
<script>
function append(o){
	document.getElementById('q').value += o + ' ';
	e.focus();
}
function operator(o){
	var e = document.getElementById('q');
	var val = e.value.trim().split(' ');
	var d = val.push(o);
	e.value = val.join(' ') + " ";
	e.focus();
}
function digit(d){
	var e = document.getElementById('q');
	var val = e.value;
	e.value = val + d;
	e.focus();
}
function del(o){
	var e = document.getElementById('q');
	var val = e.value.trim().split(' ');
	var d = val.pop();
	e.value = val.join(' ') + " ";
	e.focus();
}
</script>
<style>
.parser {
	background-color: #444;
	color: #ddd;
	padding: 4px;
	font-size: 11pt;
}
.display {
	background-color: #bcb;
	background: repeating-linear-gradient(
		to right,
		#bbccbb,
		#bbccbb 15px,
		#b0c0b0 15px,
		#b0c0b0 30px
	);
}
.display, input {
	color: #252;
	padding: 4px;
	font-family:monospace;
	font-size: 18pt;
	border: 2px inset #696;
	outline: none;
}
input {
	background-color: #ada;
	color: #040;
}
input:focus {
	border-color: #7a7;
}
.button {
	font-size: 16pt;
	background-color: #655;
	color: #fff;
	border-radius: 4px;
	padding: 7px 8px;
	border: 2px outset #544;
	display:inline-block;
	box-sizing: border-box;
	line-height: 0.8em;
	outline: none;
}
.button:hover {
	background-color: #766;
	cursor: pointer;
}
.operators, .operands {
	font-size: 14pt;
}
.operator, .operand {
	border: 1px outset #ccc;
	display:inline-block;
	margin: 4px;
	padding: 10px;
	background-color: #eee;
	width: 3em;
	height: 2em;
	line-height: 2em;
	vertical-align: middle;
	text-align: center;
	border-radius: 4px;
}
.cell {
	display:inline-block;
	vertical-align:top;
	max-width: 300px;
}
a {
	color: #000;
	text-decoration: none;
}
a:hover div {
	border: 1px solid #aaa;
}
a:active div {
	border: 1px inset #aaa;
}
.complex div { background-color: #edd; }
.binary  div { background-color: #ddd; }
.trig    div { background-color: #ded; }
.digits  div { background-color: #eed; }
.base    div { background-color: #ddf; }
.op      div { background-color: #dee; }
.part    div { background-color: #eee0d2; }
.stack   div { background-color: #dce; }
</style>
</head>
<body>

	<h1>RPN Calculator</h1>
	<h2>Parser output</h2>
	<pre class='parser'><?php

		require_once('../autoload.php');

		use App\Parser;
		use App\Calculator;
		use App\NonCommutativeStack as Stack;
		
		$q = $_GET['q'] ?? '';

		$calc = new Calculator( new Stack );
		$parser = new Parser( $calc );

		$parser->verbose = true;
		$parser->parse($q);

	?></pre>
	<h2>Calculator display and input</h2>
	<div class='display'><?= $calc->display() ?: '&nbsp;'; ?></div>

	<form>
		<input autofocus onfocus="this.value = this.value.trim() + ' ';" size="60" id="q" name="q" value="<?= $q ?>" />
		<button class='button'>Enter</button>
		<button class='button' onclick="javascript:del();return false;">&larr;</button>
	</form>

	<h2>Keypad</h2>

		<div class='operands'>
			<span class='complex'>
				<a href="javascript:operator('re')"><div class='operator'>re</div></a>
				<a href="javascript:operator('im')"><div class='operator'>im</div></a>
			</span>
			<span class='part'>
				<a href="javascript:operator('int')"><div class='operator'>int</div></a>
				<a href="javascript:operator('frac')"><div class='operator'>frac</div></a>
				<a href="javascript:operator('round')"><div class='operator'>round</div></a>
			</span>
			<span class='base'>
				<a href="javascript:operator('dec')"><div class='operator'>dec</div></a>
				<a href="javascript:operator('bin')"><div class='operator'>bin</div></a>
				<a href="javascript:operator('oct')"><div class='operator'>oct</div></a>
				<a href="javascript:operator('hex')"><div class='operator'>hex</div></a>
			</span>
			<br />
			<span class='complex'>
				<a href="javascript:operator('mag')"><div class='operator'>mag</div></a>
				<a href="javascript:operator('arg')"><div class='operator'>arg</div></a>
			</span>
			<span class='binary'>
				<a href="javascript:operator('and')"><div class='operator'>and</div></a>
				<a href="javascript:operator('or')"><div class='operator'>or</div></a>
			</span>
			<span class='op'>
				<a href="javascript:operator('^')"><div class='operator'>^</div></a>
				<a href="javascript:operator('1/x')"><div class='operator'>1/x</div></a>
				<a href="javascript:operator('-x')"><div class='operator'>-x</div></a>
				<a href="javascript:operator('mod')"><div class='operator'>mod</div></a>
				<a href="javascript:operator('/')"><div class='operator'>/</div></a>
			</span>
			<br />
			<a class='stack' href="javascript:operator('swap')"><div class='operator'>swap</div></a>
			<span class='complex'>
				<a href="javascript:operator('conj')"><div class='operator'>conj</div></a>
			</span>
			<span class='binary'>
				<a href="javascript:operator('not')"><div class='operator'>not</div></a>
				<a href="javascript:operator('xor')"><div class='operator'>xor</div></a>
			</span>
			<a class='op' href="javascript:operator('sqrt')"><div class='operator'>sqrt</div></a>
			<span class='digits'>
				<a href="javascript:digit(7)"><div class='operand'>7</div></a>
				<a href="javascript:digit(8)"><div class='operand'>8</div></a>
				<a href="javascript:digit(9)"><div class='operand'>9</div></a>
			</span>
			<a class='op' href="javascript:operator('*')"><div class='operator'>*</div></a>
			<br />
			<a class='stack' href="javascript:operator('pop')"><div class='operator'>pop</div></a>
			<a class='stack' href="javascript:operator('push')"><div class='operator'>push</div></a>
			<span class='binary'>
				<a href="javascript:operator('shl')"><div class='operator'>shl</div></a>
				<a href="javascript:operator('shr')"><div class='operator'>shr</div></a>
			</span>
			<a class='op' href="javascript:operator('ln')"><div class='operator'>ln</div></a>
			<span class='digits'>
				<a href="javascript:digit(4)"><div class='operand'>4</div></a>
				<a href="javascript:digit(5)"><div class='operand'>5</div></a>
				<a href="javascript:digit(6)"><div class='operand'>6</div></a>
			</span>
			<a class='op' href="javascript:operator('-')"><div class='operator'>-</div></a>
			<br />
			<span class='trig'>
				<a href="javascript:operator('deg')"><div class='operand'>deg</div></a>
				<a href="javascript:operator('sin')"><div class='operator'>sin</div></a>
				<a href="javascript:operator('cos')"><div class='operator'>cos</div></a>
				<a href="javascript:operator('tan')"><div class='operator'>tan</div></a>
			</span>
			<a class='op' href="javascript:operator('nthlog')"><div class='operator'>nthlog</div></a>
			<span class='digits'>
				<a href="javascript:digit(1)"><div class='operand'>1</div></a>
				<a href="javascript:digit(2)"><div class='operand'>2</div></a>
				<a href="javascript:digit(3)"><div class='operand'>3</div></a>
			</span>
			<a class='op' href="javascript:operator('+')"><div class='operator'>+</div></a>
			<br />
			<span class='trig'>
				<a href="javascript:operator('rad')"><div class='operand'>rad</div></a>
				<a href="javascript:digit('e')"><div class='operand'>e</div></a>
				<a href="javascript:digit('i')"><div class='operand'>i</div></a>
				<a href="javascript:digit('π')"><div class='operand'>π</div></a>
			</span>
			<a href="javascript:append('nan')"><div class='operand'>nan</div></a>
			<span class='digits'>
				<a href="javascript:digit(0)"><div class='operand'>0</div></a>
				<a href="javascript:digit('00')"><div class='operand'>00</div></a>
				<a href="javascript:digit('.')"><div class='operand'>.</div></a>
			</span>
			<a class='stack' href="javascript:operator('dump')"><div class='operator'>dump</div></a>
			<br />
		</div>
		<?php /* foreach( App\OperandFactory::reference() as $op ): ?>
			<a href="javascript:append('<?= $op ?>')">
				<div class='operand'>
					<?= $op ?>
				</div>
			</a>
		<?php endforeach; */ ?>
	</div>

		<?php /* foreach( App\OperatorFactory::reference() as $op ): ?>
	<h2>Operators</h2>
	<div class='operators'>
			<a href="javascript:operator('<?= $op ?>')">
				<div class='operator'><?= $op ?></div>
			</a>
	</div>
		<?php endforeach; */ ?>


</body>
</html>
