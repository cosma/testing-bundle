test:
	vendor/phpunit/phpunit/phpunit -c phpunit.xml.dist Tests/

coverage:
	vendor/phpunit/phpunit/phpunit -c phpunit.xml.dist --coverage-text --coverage-html=Tests/coverage Tests/