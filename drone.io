##
# Install php project dependencies through composer
##
sudo composer self-update
composer install --prefer-source


##
# Run tests
##

vendor/phpunit/phpunit/phpunit --coverage-text --coverage-html=Tests/coverage Tests