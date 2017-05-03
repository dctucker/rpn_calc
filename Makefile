.PHONY: test

all: test

test:
	phpunit --bootstrap autoload.php tests
	./rpn_calc.php -v "2 i * 5 + 3 i * 1 + *"
