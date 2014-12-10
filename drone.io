##
# Install php project dependencies through composer
##
composer install --prefer-source


##
# Run tests
##

phpunit --coverage-text --coverage-html=Tests/coverage Tests