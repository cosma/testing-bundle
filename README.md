Testing Bundle
===============


[![Circle CI](https://circleci.com/gh/cosma/testing-bundle.svg?style=svg)](https://circleci.com/gh/cosma/testing-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7697e84e-fd7f-47ae-97cf-66a266c9b4c0/mini.png)](https://insight.sensiolabs.com/projects/7697e84e-fd7f-47ae-97cf-66a266c9b4c0)



An extension of [h4cc/AliceFixturesBundle](https://github.com/h4cc/AliceFixturesBundle) , a Symfony2 bundle for flexible usage of  [nelmio/alice](https://github.com/nelmio/alice)  fixtures integrated with very powerful data generator  [fzaninotto/Faker](https://github.com/fzaninotto/Faker).
This bundle integrates [mockery/mockery](https://github.com/padraic/mockery) library, too.
The Testing Bundle bundle works with data fixtures in .yml format, detached from the common Doctrine DataFixtures.
There are multiple ways of loading fixture files.
The Testing Bundle offers loading Fixtures from .yml ,  dropping and recreating the ORM Schema.



# Table of Contents
 - [Installation](#installation)
 - [Configuration](#configuration)
 - [Test Cases](#test-cases)
 - [Fixtures](#fixtures)
 - [Advanced Usage](#advanced-usage)
 - [Run Tests](#run-tests)
 - [License](#license)


# Installation
    

```bash
    $   php composer.phar require cosma/testing-bundle '2.0.*'
```
Follow the 'dev-master' branch for latest dev version. But i recommend to use more stable version tags if available.


After that, add the h4ccAliceFixturesBundle and TestingBundle to your Kernel, most likely in the "dev" or "test" environment.

```php
# app/AppKernel.php

<?php

public function registerBundles()
{
    $bundles = array(
        // ...
    );

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        // ...
        $bundles[] = new h4cc\AliceFixturesBundle\h4ccAliceFixturesBundle();
        $bundles[] = new Cosma\Bundle\TestingBundle\TestingBundle();
    }
}
```


# Configuration

In case you want to change default paths of fixture directory you can configure the testing bundle's fixture_path. 
This sets a new relative path to the fixture directory in your bundle.

```yaml
# app/config/config_test.yml

cosma_testing:
    fixture_directory: Fixture
    tests_directory: Tests
    doctrine:
        cleaning_strategy: truncate # drop - to drop database
    solarium:
        host: 127.0.0.1
        port: 8080
        path: /solr
        core: test
        timeout: 10
    elastica:
        host: 127.0.0.1
        port: 9200
        path: /
        timeout: 10   
        index: test
    selenium:
        remote_server_url: http://127.0.0.1:4444/wd/hub
        test_domain: example.com   
    redis:
        scheme: tcp
        host: 127.0.0.1
        port: 6379
        database: 13
        timeout: 5      
```



# Test Cases


Supports the following Test Cases:

* [Simple Test Case](#simple-test-case)
* [Web Test Case](#web-test-case)
* [DB Test Case](#db-test-case)
* [Solr Test Case](#solr-test-case)
* [Elastic Search Test Case](#elastic-search-test-case)
* [Selenium Test Case](#selenium-test-case)
* [Redis Test Case](#redis-test-case)
* [Composed Test Cases](#composed-test-cases)


## Simple Test Case
This case is an extension of PHPUnit_Framework_TestCase, with two extra simple methods:

* **getMockedEntityWithId** ($entity, $id)
* **getEntityWithId** ($entity, $id)
* **getTestClassPath** ()

```php
use Cosma\Bundle\TestingBundle\TestCase\SimpleTestCase;
 
class SomeVerySimpleUnitTest extends SimpleTestCase
{
    public function testSomething()
    {
        $mockedUserFullNamespace = $this->getMockedEntityWithId('Acme\AppBundle\Entity\User', 1);
        
        $mockedUserBundleNamespace = $this->getMockedEntityWithId('AppBundle:User', 2);
         
        $userFullNamespace = $this->getEntityWithId('Acme\AppBundle\Entity\User', 3);
                
        $userBundleNamespace = $this->getEntityWithId('AppBundle:User', 4); 
        
        $thisTestClassPath = $this->getTestClassPath(); 
    }
}
```
 
 
## Web Test Case
This case is an extension of Symfony2 WebTestCase -  Symfony\Bundle\FrameworkBundle\Test\WebTestCase
It has the following methods:

* **getKernel** ()
* **getContainer** ()
* **getClient** (array $server)

```php
use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;

class SomeWebFunctionalTest extends WebTestCase
{
    public function setUp()
    {
        /**
        * Required call that boots the Symfony kernel
        */
        parent::setUp();
    }
    
    public function testSomething()
    {
        $kernel = $this->getKernel();
        
        $container = $this->getContainer();
            
        // Client for functional tests. Emulates a browser
        $client = $this->getClient();
    }
}
```


## DB Test Case
This case is an extension of Symfony WebTestCase with Database and fixtures support  
It has the following methods:

* **dropDatabase** ()
* **loadFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **getEntityManager** ()
* **getEntityRepository** ($entity)
* **getFixtureManager** ()

```php
use Cosma\Bundle\TestingBundle\TestCase\DBTestCase;

class SomeFunctionalWebDBTest extends WebTestCase
{
    public function setUp()
    {
        /**
        * Required call that boots the Symfony kernel
        */
        parent::setUp();
        
        /**
        * drops database tables before every test. 
        * has two strategies set by parameter cosma_testing.doctrine.cleaning_strategy:
        * 1. truncate (default,  faster)
        * 2. drop     (actual drop, slower)
        */
        $this->dropDatabase();

        /**
         * 1. Truncates the tables user and group(default behaviour)
         * 2. Loads two fixtures files located in src/AppBundle/Fixture/Table/User.yml and src/AnotherBundle/Fixture/Table/Group.yml
         * 
         */
        $this->loadFixtures(
                        [
                            'AppBundle:Table:User', 
                            'AnotherBundle:Table:Group'
                        ]
        );

        /**
         * Loads a fixtures file located in src/SomeBundle/Fixture/SomeDirectory/Book.yml
         * Doesn't truncate the table 
         */
        $this->loadFixtures(
                        [
                            'SomeBundle:SomeDirectory:Book'
                        ],
                        false
        );
    }
    
    public function testSomething()
    {
        /**
         * Fixtures can be load inside a test, too.
         */
        $this->loadFixtures(['SomeBundle:SomeDirectory:Author']);

        $entityManager = $this->getEntityManager();
                
        $entityRepository = $this->getEntityRepository('AppBundle:User');
        
        $fixtureManager = $this->getFixtureManager();
    }
}
```

## Solr Test Case
This case is an extension of WebTestCase, from current bundle, with extra Solr support
It has the following methods:

* **getSolariumClient** ()
 
```php
use Cosma\Bundle\TestingBundle\TestCase\SolrTestCase;

class SomeSolrTest extends SolrTestCase
{
    public function setUp()
    {
        /**
        * Required call that boots the Symfony kernel and truncate default test Solr core
        */
        parent::setUp();
    }

    public function testIndex()
    {
        $solariumClient = $this->getSolariumClient();
        
        /**
         * get an update query instance
         */
        $update = $solariumClient->createUpdate();

        /**
         * first fixture document
         */
        $documentOne = $update->createDocument();
        $documentOne->id = 123;
        $documentOne->name = 'testdoc-1';
        $documentOne->price = 364;

        /**
         * second fixture document
         */
        $documentTwo = $update->createDocument();
        $documentTwo->id = 124;
        $documentTwo->name = 'testdoc-2';
        $documentTwo->price = 340;

        /**
         * add the documents and a commit command to the update query
         */
        $update->addDocuments([$documentOne, $documentTwo]);
        $update->addCommit();

        /**
         * execute query
         */
        $solariumClient->update($update);
    }
}
```



## Elastic Search Test Case
This case is an extension of WebTestCase, from current bundle, with extra ElasticSearch support
It has the following methods:

* **getElasticIndex** ()
* **getElasticClient** ()


```php
class SomeElasticTest extends ElasticTestCase
{
    public function setUp()
    {
        /**
        * Required call that boots the Symfony kernel and recreates default test elastic index
        */
        parent::setUp();
    }

    public function testSomethingElastic()
    {
        $elasticIndex = $this->getElasticIndex();
        
        $elasticClient = $this->getElasticClient();
        
        $anotherElasticIndex = $elasticaClient->getIndex('another_index');
        
        //Create a type
        $elasticType = $elasticIndex->getType('tweet');
        
        
        // Define mapping
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($elasticaType);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');
        
        // Define boost field
        $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));
        
        // Set mapping
        $mapping->setProperties(array(
            'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'user'    => array(
                'type' => 'object',
                'properties' => array(
                    'name'      => array('type' => 'string', 'include_in_all' => TRUE),
                    'fullName'  => array('type' => 'string', 'include_in_all' => TRUE)
                ),
            ),
            'msg'     => array('type' => 'string', 'include_in_all' => TRUE),
            'tstamp'  => array('type' => 'date', 'include_in_all' => FALSE),
            'location'=> array('type' => 'geo_point', 'include_in_all' => FALSE),
            '_boost'  => array('type' => 'float', 'include_in_all' => FALSE)
        ));
        
        // Send mapping to type
        $mapping->send();


        /**
         * first fixture document
         */
        $idOne = 1;
        $dataOne = array(
            'id'      => $idOne,
            'user'    => array(
                'name'      => 'mewantcookie',
                'fullName'  => 'Cookie Monster'
            ),
            'msg'     => 'Me wish there were expression for cookies ',
            'tstamp'  => '1238081389',
            'location'=> '41.12,-71.34',
            '_boost'  => 1.0
        );

        $documentOne = new \Elastica\Document($idOne, $dataOne);


        /**
         * second fixture document
         */
        $idTwo = 2;
        $dataTwo = array(
            'id'      => $idTwo,
            'user'    => array(
                'name'      => 'shewantcookie',
                'fullName'  => 'Cookie Witch'
            ),
            'msg'     => 'blah blah blah expresion.',
            'tstamp'  => '143567',
            'location'=> '43.12,-78.34',
            '_boost'  => 3.0
        );

        $documentTwo = new \Elastica\Document($idTwo, $dataTwo);

        /**
         * add documents to type
         */
        $elasticType->addDocument($documentOne);
        $elasticType->addDocument($documentTwo);

        /**
         * refresh index
         */
        $elasticType->getIndex()->refresh();
    }
}
```


## Selenium Test Case
This case is an extension of WebTestCase, with extra Selenium support
It has the following methods:

* **getRemoteWebDriver** ()
* **getTestDomain** ()
* **open** ($url)
* **openSecure** ($url)

```php
use Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase;

class SomeSeleniumTest extends SeleniumTestCase
{

    public function setUp()
    {
        /**
        * Required call that boots the Symfony kernel and initialize selenium remote web driver
        */
        parent::setUp();
    }

    /**
     * read title from google site
     */
    public function testGoogleTitle()
    {
    
        $remoteWebDriver = $this->getRemoteWebDriver();
        $domain = $this->getTestDomain();
    
        // open http url http://testdomain/somepage.html 
        $webDriver = $this->open('/somepage.html');
        $this->assertContains('Some Title', $webDriver->getTitle());
        
        // open https url https://testdomain/securePage.html 
        $webDriver = $this->openSecure('/securePage.html');
        $this->assertContains('Some Title', $webDriver->getTitle());
    }
}
```


## Redis Test Case
This case is an extension of WebTestCase, with extra Redis support
It has the following methods:

* **getRedisClient** ()

```php
use Cosma\Bundle\TestingBundle\TestCase\RedisTestCase;

class SomeSeleniumTest extends RedisTestCase
{

    public function setUp()
    {
        /**
        * Required call that boots the Symfony kernel and initialize selenium remote web driver
        */
        parent::setUp();
    }

    /**
     * read title from google site
     */
    public function testGoogleTitle()
    {
        $redisClient = $this->getRedisClient();
        
        $redisClient->set('key' , 'value');
    }
}
```

## Composed Test Cases
You can build composed Test Cases using the following defined traits under \Cosma\Bundle\TestingBundle\TestCase\Traits:
Supports following test cases:

* SimpleTrait
* DBTrait
* CommandTrait
* ElasticTrait
* SolrTrait
* SeleniumTrait
* RedisTrait


All composed TestCases can use one or more traits and extends Cosma\Bundle\TestingBundle\TestCase\WebTestCase 

```php
namespace Acme\AppBundle\TestCase;

use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;

// add the rest of traits
use Cosma\Bundle\TestingBundle\TestCase\Traits\DBTrait;
use Cosma\Bundle\TestingBundle\TestCase\Traits\ElasticTrait;
use Cosma\Bundle\TestingBundle\TestCase\Traits\SeleniumTrait;
use Cosma\Bundle\TestingBundle\TestCase\Traits\RedisTrait;

abstract class ComposedTestCase extends WebTestCase
{
    /**
    *   This Test Case combines: DB, Elastic and Selenium Test Cases 
    */
    use DBTrait;
    use ElasticTrait;
    use SeleniumTrait;
    use RedisTrait;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->getFixtureManager();     // from DBTrait
        $this->recreateIndex();         // from ElasticTrait
        $this->getRemoteWebDriver();    // from SeleniumTrait
        $this->resetRedisDatabase();    // from RedisTrait
    }
}
```


# Fixtures

[Alice](https://github.com/nelmio/alice) fixtures are integrated with [Faker](https://github.com/fzaninotto/Faker).

The most basic functionality of [Alice](https://github.com/nelmio/alice) is to turn flat yaml files into objects. 

You can define many objects of different classes in one file as such:

```yaml
Nelmio\Entity\User:
    user{1..10}:
        username: <username()>
        fullname: <firstName()> <lastName()>
        birthDate: <date()>
        email: <email()>
        favoriteNumber: <numberBetween(1, 200)>

Nelmio\Entity\Group:
    group1:
        name: Admins  
        users: [@user1, @user4, @user7]      
```

## Importing/Exporting Fixture Files

You can easily dump Database data to Yaml fixture files with the command cosma_testing:fixtures:dump

```bash
    # Argument :: dump directory - required
    # Argument :: entity  - if not specified will save all entities : default *
    # Option :: --associations / -a - saves the associations between entities, too
        
    $   php app/console cosma_testing:fixtures:export [-a|--associations] dumpDirectory [entity]
    
    $   php app/console cosma_testing:fixtures:export -a "path/to/dump/directory" BundleName:Entity
```


You can easily import Yaml fixture to Database with command h4cc_alice_fixtures:load:files

```bash
    # Argument :: list of files to import : required
    # Option :: --type / - t : Type of loader. Can be "yaml" or "php" : yaml default
    # Option :: --drop / -d : drop and create schema before loading
    # Option :: --no-persist / - np :  persist loaded entities in database
         
    $   php app/console cosma_testing:fixtures:import [--drop] /path/to/fixtureFileOne.yml  /path/to/fixtureFileTwo.yml
```


# Advanced Usage

## Adding own Providers for Faker

A provider for Faker can be any class, that has public methods.
These methods can be used in the fixture files for own testdata or even calculations.
To register a provider, create a service and tag it.

Example:

```yaml
services:
    your.faker.provider:
        class: YourProviderClass
        tags:
            -  { name: h4cc_alice_fixtures.provider }
```


## Adding own Processors for Alice

A alice processor can be used to manipulate a object _before_ and _after_ persisting.
To register a own processor, create a service and tag it.

Example:

```yaml
services:
    your.alice.processor:
        class: YourProcessorClass
        tags:
            -  { name: h4cc_alice_fixtures.processor }
```


## Mockery

[Mockery](https://github.com/padraic/mockery) is a simple yet flexible PHP mock object framework for use in unit testing

```php
use Cosma\Bundle\TestingBundle\TestCase\SimpleTestCase;
       
class SomeUnitTest extends SimpleTestCase
{
    public function testGetsAverageTemperatureFromThreeServiceReadings()
    {
        $service = \Mockery::mock('service');
        $service->shouldReceive('readTemp')->times(3)->andReturn(10, 12, 14);

        $temperature = new Temperature($service);

        $this->assertEquals(12, $temperature->average());
    }
}    
```


# Run Tests

vendor/phpunit/phpunit/phpunit -c phpunit.xml.dist --coverage-text --coverage-html=Tests/coverage Tests


# License

The bundle is licensed under MIT.
