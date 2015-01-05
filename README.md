Testing Bundle
================

An extension of [h4cc/AliceFixturesBundle](https://github.com/h4cc/AliceFixturesBundle) , a Symfony2 bundle for flexible usage of  [nelmio/alice](https://github.com/nelmio/alice)  fixtures integrated with very powerful data generator  [fzaninotto/Faker](https://github.com/fzaninotto/Faker).


[![Build Status](https://drone.io/bitbucket.org/cosma/testing-bundle/status.png)](https://drone.io/bitbucket.org/cosma/testing-bundle/latest)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7697e84e-fd7f-47ae-97cf-66a266c9b4c0/mini.png)](https://insight.sensiolabs.com/projects/7697e84e-fd7f-47ae-97cf-66a266c9b4c0)



Supports following test cases:

* SimpleTestCase
* WebTestCase
* SolrTestCase
* ElasticTestCase
* SeleniumTestCase




## Introduction

This bundle works with data fixtures in .yml format, detached from the common Doctrine DataFixtures.
There are multiple ways of loading fixture files.
This bundle offers loading Fixtures from .yml ,  dropping and recreating the ORM Schema.



## Installation

```bash
$ php composer.phar require cosma/TestingBundle
```
Follow the 'dev-master' branch for latest dev version. But i recommend to use more stable version tags if available.


After that, add the h4ccAliceFixturesBundle and TestingBundle to your Kernel, most likely in the "dev" or "test" environment.

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
        $bundles[] = new h4cc\AliceFixturesBundle\h4ccAliceFixturesBundle();
        $bundles[] = new Cosma\Bundle\TestingBundle\TestingBundle();
    }
}
```



## Configuration

In case you want to change default paths of fixture directory you can configure the testing bundle's fixture_path. 
This sets a new relative path to the fixture directory in your bundle.

```yaml
# app/config/config_test.yml

cosma_testing:
    fixture_path: Fixture
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
        type: test
```



## Usage

### Test Cases


#### Simple Test Case
This case is an extension of PHPUnit_Framework_TestCase, with two extra simple methods:

* **getMockedEntityWithId** ($entityNamespaceClass, $id)
* **getEntityWithId** ($entityNamespaceClass, $id)


```php
use Cosma\Bundle\TestingBundle\TestCase\SimpleTestCase;
 
class SomeUnitTest extends SimpleTestCase
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
        // is using the value of configuration parameter entity_namespace
        $userRelative = $this->getEntityWithId('User', 12); 
    }
}
```
 


#### Web Test Case
This case is an extension of Symfony WebTestCase, the functional test case in Symfony2 
It has the following methods:

* **loadTableFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **loadTestFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **loadCustomFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **getClient** ()
* **getContainer** ()
* **getEntityManager** ()
* **getEntityRepository** ()

* **getMockedEntityWithId** ($entityNamespaceClass, $id)
* **getEntityWithId** ($entityNamespaceClass, $id)




```php
use Cosma\Bundle\TestingBundle\TestCase\WebTestCase;

class SomeFunctionalTest extends WebTestCase
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
        $mockedUserAbsolute = $this->getMockedEntityWithId('Acme\DemoBundle\Entity\User', 11);
        
        $mockedUserRelative = $this->getMockedEntityWithId('User', 1200);
        
        $userAbsolute = $this->getEntityWithId('Acme\DemoBundle\Entity\User', 134);
        
        $userRelative = $this->getEntityWithId('User', 12);
        
        /**
        *  Client for functional tests. Emulates a browser
        */
        $client = $this->getClient();
        
        /**
        *  EntityManager - Doctrine
        */
        $entityManager = $this->getEntityManger();
        
        /**
        *  EntityRepository for User
        */
        $userRepository = $this->getEntityRepository('User');
    }
}
```



#### Solr Test Case
This case is an extension of WebTestCase, from current bundle, with extra Solr support
It has the following methods:

* **getSolariumClient** ()

* **loadTableFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **loadTestFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **loadCustomFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **getClient** ()
* **getContainer** ()
* **getEntityManager** ()
* **getEntityRepository** ()

* **getMockedEntityWithId** ($entityNamespaceClass, $id)
* **getEntityWithId** ($entityNamespaceClass, $id)


```php
use Cosma\Bundle\TestingBundle\TestCase\SolrTestCase;

class SomeSolrTest extends SolrTestCase
{
    public function setUp()
    {
        parent::setUp();
        
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
        $update->addDocuments(array($documentOne, $documentTwo));
        $update->addCommit();
        
        /**
         * execute query
         */
        $solariumClient->update($update);
    }
```



#### ElasticSearch Test Case
This case is an extension of WebTestCase, from current bundle, with extra ElasticSearch support
It has the following methods:

* **getElasticType** ()
* **getElasticIndex** ()
* **getElasticClient** ()

* **loadTableFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **loadTestFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **loadCustomFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **getClient** ()
* **getContainer** ()
* **getEntityManager** ()
* **getEntityRepository** ()

* **getMockedEntityWithId** ($entityNamespaceClass, $id)
* **getEntityWithId** ($entityNamespaceClass, $id)


```php
use Cosma\Bundle\TestingBundle\TestCase\ElasticTestCase;

class SomeElasticTest extends ElasticTestCase
{
    public function setUp()
    {
        parent::setUp();

        $elasticType = $this->getElasticType();


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
```



#### Selenium Test Case
This case is an extension of WebTestCase, from current bundle, with extra Selenium support
It has the following methods:

* **open** ($url)
* **getDomain** ()

* **loadTableFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **loadTestFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **loadCustomFixtures** (array $fixtures, $dropDatabaseBefore = true)
* **getClient** ()
* **getContainer** ()
* **getEntityManager** ()
* **getEntityRepository** ()

* **getMockedEntityWithId** ($entityNamespaceClass, $id)
* **getEntityWithId** ($entityNamespaceClass, $id)


```php
use Cosma\Bundle\TestingBundle\TestCase\SeleniumTestCase;

class SomeSeleniumTest extends SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();

        $elasticType = $this->getElasticType();


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
```


### Fixtures

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


### Adding own Providers for Faker

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


### Adding own Processors for Alice

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




### Run Tests ###

vendor/phpunit/phpunit/phpunit -c phpunit.xml.dist --coverage-text --coverage-html=Tests/coverage Tests




## License

The bundle is licensed under MIT.