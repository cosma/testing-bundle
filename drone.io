##
# Install php project dependencies through composer
##
composer install --prefer-source


##
# Run tests
##

vendor/phpunit/phpunit/phpunit -c phpunit.xml.dist Tests