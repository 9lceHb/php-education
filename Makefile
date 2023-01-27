install:
	composer install

console:
	composer exec --verbose psysh

lint:
	composer run-script phpcs -- --standard=PSR12 bin src tests
lint2:
	composer exec --verbose phpstan

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 src tests

test:
	composer exec --verbose phpunit tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

validate:
	composer validate

autoload:
	composer dump-autoload
gendiff:
	bin/gendiff