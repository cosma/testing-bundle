Testing Bundle
================

An extension of [h4cc/AliceFixturesBundle](https://github.com/h4cc/AliceFixturesBundle) , a Symfony2 bundle for flexible usage of [nelmio/alice](https://github.com/nelmio/alice) and [fzaninotto/Faker](https://github.com/fzaninotto/Faker) in Symfony2.

[![Build Status](https://drone.io/bitbucket.org/cosma/testing-bundle/status.png)](https://drone.io/bitbucket.org/cosma/testing-bundle/latest)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7697e84e-fd7f-47ae-97cf-66a266c9b4c0/mini.png)](https://insight.sensiolabs.com/projects/7697e84e-fd7f-47ae-97cf-66a266c9b4c0)



## Introduction

This bundle works with data fixtures in .yml format, detached from the common Doctrine DataFixtures.
There are multiple ways of loading fixture files.
This bundle offers loading Fixtures from .yml ,  dropping and recreating the ORM Schema.



## Installation

```bash
$ php composer.phar require cosma/TestingBundle
```
Follow the 'dev-master' branch for latest dev version. But i recommend to use more stable version tags if available.


After that, add the Bundle to your Kernel, most likely in the "dev" or "test" environment.

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
    );

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        // ...
        $bundles[] = new Cosma\Bundle\TestingBundle();
    }
}
```



## Configuration

In case you want to change default paths of Fixture and Entities in your bundle:
* fixture_path - relative path to the fixture directory in your bundle
* entity_namespace - relative namespace to the entities directory in your bundle

```yaml
# app/config/config_dev.yml

cosma_testing:
    fixture_path: Fixture             # default
    entity_namespace: Entity          # default
```



## Usage

### Test Cases


#### Simple TestCase
Is an extension of PHPUnit_Framework_TestCase,   the simplest test case in PHPUnit
This test case has two methods to load a real or a mocked Doctrine Entity with a set Id:

** getMockedEntityWithId ($entityNamespaceClass, $id)

** getEntityWithId ($entityNamespaceClass, $id)


```php
use Cosma\Bundle\TestingBundle\TestCase\SimpleTestCase;
 
class SomeTestClass extends SimpleTestCase
{
    public function testSomething()
    {
        // custom namespace mock entity
        $mockedUserAbsolute = $this->getMockedEntityWithId('Acme\DemoBundle\Entity\User', 12345);
        
        // relative namespace mocked entity using the value of configuration parameter entity_namespace
        $mockedUserRelative = $this->getMockedEntityWithId('User', 1200);
         
        // custom namespace entity without dropping database
        $userAbsolute = $this->getEntityWithId('Acme\DemoBundle\Entity\User', 134);
        
        // relative namespace entity using the value of configuration parameter entity_namespace
        $userRelative = $this->getEntityWithId('User', 12); // is using the value of configuration parameter entity_namespace
    }
}
```
 


#### Web Test Case
Is an extension of WebTestCase,  the functional test case in Symfony2 
Has  available the following methods:

* getMockedEntityWithId ($entityNamespaceClass, $id)
* getEntityWithId ($entityNamespaceClass, $id)
* loadTableFixtures (array $fixtures, $dropDatabaseBefore = true)
* loadTestFixtures (array $fixtures, $dropDatabaseBefore = true)
* loadCustomFixtures (array $fixtures, $dropDatabaseBefore = true)
* getClient ()
* getContainer ()
* getEntityManager ()
* getEntityRepository ()












```php
use Cosma\Bundle\TestingBundle\TestCase\SimpleTestCase;

class SomeTestClass extends SimpleTestCase
{
    public function setUp()
    {
        /**
         * Fixtures loaded the table directory where mostly resides data for DB tables
         * Data from one table is in one file.
         */
        $this->loadTableFixtures(array('User', 'Group'));

        /**
         * Fixtures from test specific directory path.
         * Every test has its own file.
         */
        $this->loadTestFixtures(array('Car', 'Series'), false );

        /**
         *  Custom path fixture with
         *  No database dropping(default behaviour)
         */
        $this->loadCustomFixtures(array('/var/www/Acme/BundleDemo/Fixture/Colleague'), false);

    }



    public function testSomething()
    {
        $mockedUserAbsolute = $this->getMockedEntityWithId('Acme\DemoBundle\Entity\User', 12345);
        
        $mockedUserRelative = $this->getMockedEntityWithId('User', 1200);
        
        $userAbsolute = $this->getEntityWithId('Acme\DemoBundle\Entity\User', 134);
        
        $userRelative = $this->getEntityWithId('User', 12);
    }
}
```








### How do I get set up? ###

* Summary of set up
* Configuration
* Dependencies
* Database configuration
* How to run tests
* Deployment instructions

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact

### Run Tests ###

vendor/phpunit/phpunit/phpunit -c phpunit.xml.dist --coverage-text  --coverage-html=Tests/coverage Tests


* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)