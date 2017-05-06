.PHONY: test

all: test

test:
	./rpn_calc.php -v "2 i * 5 + 3 i * 1 + * 2 i * 4 + /"
	phpunit --coverage-html coverage --whitelist src --bootstrap autoload.php tests
