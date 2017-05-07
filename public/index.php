<html charset='utf8'>
<head>
<title>RPN Calc</title>
<style>
<?php require_once('index.css') ?>
</style>
<script>
function append(o){
	var e = document.getElementById('q');
	var val = e.value.trim().split(' ');
	var d = val.push(o);
	e.value = val.join(' ') + " ";
	e.focus();
}
function digit(d){
	var e = document.getElementById('q');
	var val = e.value;
	e.value = val + d.trim();
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
</head>
<body>
<?php

require_once('../autoload.php');

use App\Parser;
use App\Calculator;
use App\NonCommutativeStack as Stack;

$q = $_GET['q'] ?? '';

$calc   = new Calculator( new Stack );
$parser = new Parser( $calc );

$parser->verbose = true;
ob_start();
$parser->parse($q);
$parser_output = ob_get_contents();
ob_end_clean();

?>

	<h1>RPN Calculator</h1>

	<div class='cell'>

		<h2>Calculator display and input</h2>
		<div class='display'><?= $calc->display() ?: '&nbsp;'; ?></div>

		<form>
			<input autofocus onfocus="this.value = this.value;" size="40" id="q" name="q" value="<?= $q ?>" />
			<button class='button'>Enter</button>
			<button class='button' onclick="javascript:del();return false;">&larr;</button>
		</form>

		<h2>Keypad</h2>
		<?php require_once('keypad.php'); ?>

	</div>

	<div class='cell'>
		<h2>Parser output</h2>
		<pre class='parser'><?= $parser_output ?></pre>
	</div>
</body>
</html>
