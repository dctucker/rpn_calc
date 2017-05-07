<?php /* foreach( App\OperandFactory::reference() as $op ): ?>
	<a href="javascript:append('<?= $op ?>')">
		<div class='operand'>
			<?= $op ?>
		</div>
	</a>
<?php endforeach; */ ?>

<?php /* foreach( App\OperatorFactory::reference() as $op ): ?>
	<h2>Operators</h2>
	<div class='operators'>
			<a href="javascript:append('<?= $op ?>')">
				<div class='operator'><?= $op ?></div>
			</a>
	</div>
<?php endforeach; */ ?>

<div class='operands'>
	<span class='complex'>
		<a href="javascript:append('re')"><div class='operator'>re</div></a>
		<a href="javascript:append('im')"><div class='operator'>im</div></a>
	</span>
	<span class='part'>
		<a href="javascript:append('int')"><div class='operator'>int</div></a>
		<a href="javascript:append('frac')"><div class='operator'>frac</div></a>
		<a href="javascript:append('round')"><div class='operator'>round</div></a>
	</span>
	<span class='base'>
		<a href="javascript:append('dec')"><div class='operator'>dec</div></a>
		<a href="javascript:append('bin')"><div class='operator'>bin</div></a>
		<a href="javascript:append('oct')"><div class='operator'>oct</div></a>
		<a href="javascript:append('hex')"><div class='operator'>hex</div></a>
	</span>
	<br />
	<span class='complex'>
		<a href="javascript:append('mag')"><div class='operator'>mag</div></a>
		<a href="javascript:append('arg')"><div class='operator'>arg</div></a>
	</span>
	<span class='binary'>
		<a href="javascript:append('and')"><div class='operator'>and</div></a>
		<a href="javascript:append('or')"><div class='operator'>or</div></a>
	</span>
	<span class='op'>
		<a href="javascript:append('^')"><div class='operator'>^</div></a>
		<a href="javascript:append('1/x')"><div class='operator'>1/x</div></a>
		<a href="javascript:append('-x')"><div class='operator'>-x</div></a>
		<a href="javascript:append('mod')"><div class='operator'>mod</div></a>
		<a href="javascript:append('/')"><div class='operator'>/</div></a>
	</span>
	<br />
	<a class='stack' href="javascript:append('swap')"><div class='operator'>swap</div></a>
	<span class='complex'>
		<a href="javascript:append('conj')"><div class='operator'>conj</div></a>
	</span>
	<span class='binary'>
		<a href="javascript:append('not')"><div class='operator'>not</div></a>
		<a href="javascript:append('xor')"><div class='operator'>xor</div></a>
	</span>
	<a class='op' href="javascript:append('sqrt')"><div class='operator'>sqrt</div></a>
	<span class='digits'>
		<a href="javascript:digit('7')"><div class='operand'>7</div></a>
		<a href="javascript:digit('8')"><div class='operand'>8</div></a>
		<a href="javascript:digit('9')"><div class='operand'>9</div></a>
	</span>
	<a class='op' href="javascript:append('*')"><div class='operator'>*</div></a>
	<br />
	<a class='stack' href="javascript:append('pop')"><div class='operator'>pop</div></a>
	<a class='stack' href="javascript:append('push')"><div class='operator'>push</div></a>
	<span class='binary'>
		<a href="javascript:append('shl')"><div class='operator'>shl</div></a>
		<a href="javascript:append('shr')"><div class='operator'>shr</div></a>
	</span>
	<a class='op' href="javascript:append('ln')"><div class='operator'>ln</div></a>
	<span class='digits'>
		<a href="javascript:digit('4')"><div class='operand'>4</div></a>
		<a href="javascript:digit('5')"><div class='operand'>5</div></a>
		<a href="javascript:digit('6')"><div class='operand'>6</div></a>
	</span>
	<a class='op' href="javascript:append('-')"><div class='operator'>-</div></a>
	<br />
	<span class='trig'>
		<a class='base' href="javascript:append('deg')"><div class='operand'>deg</div></a>
		<a href="javascript:append('sin')"><div class='operator'>sin</div></a>
		<a href="javascript:append('cos')"><div class='operator'>cos</div></a>
		<a href="javascript:append('tan')"><div class='operator'>tan</div></a>
	</span>
	<a class='op' href="javascript:append('nthlog')"><div class='operator'>nthlog</div></a>
	<span class='digits'>
		<a href="javascript:digit('1')"><div class='operand'>1</div></a>
		<a href="javascript:digit('2')"><div class='operand'>2</div></a>
		<a href="javascript:digit('3')"><div class='operand'>3</div></a>
	</span>
	<a class='op' href="javascript:append('+')"><div class='operator'>+</div></a>
	<br />
	<span class='trig'>
		<a class='base' href="javascript:append('rad')"><div class='operand'>rad</div></a>
		<a href="javascript:append('e')"><div class='operand'>e</div></a>
		<a href="javascript:append('i')"><div class='operand'>i</div></a>
		<a href="javascript:append('π')"><div class='operand'>π</div></a>
	</span>
	<a href="javascript:append('nan')"><div class='operand'>nan</div></a>
	<span class='digits'>
		<a href="javascript:digit('0')"><div class='operand'>0</div></a>
		<a href="javascript:digit('00')"><div class='operand'>00</div></a>
		<a href="javascript:digit('.')"><div class='operand'>.</div></a>
	</span>
	<a class='stack' href="javascript:append('dump')"><div class='operator'>dump</div></a>
	<br />
</div>
