##
# Install php project dependencies through composer
##
composer install --prefer-source


##
# Run tests
##

vendor/phpunit/phpunit/phpunit --coverage-text --coverage-html=Tests/coverage Tests