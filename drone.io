##
# Install php project dependencies through composer
##
composer install --prefer-source


##
# Run tests
##

phpunit -c phpunit.xml.dist Tests