Testing Bundle
================

An extension of [h4cc/AliceFixturesBundle](https://github.com/h4cc/AliceFixturesBundle) , a Symfony2 bundle for flexible usage of [nelmio/alice](https://github.com/nelmio/alice) and [fzaninotto/Faker](https://github.com/fzaninotto/Faker) in Symfony2.

[![Build Status](https://drone.io/bitbucket.org/cosma/testing-bundle/status.png)](https://drone.io/bitbucket.org/cosma/testing-bundle/latest)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7697e84e-fd7f-47ae-97cf-66a266c9b4c0/mini.png)](https://insight.sensiolabs.com/projects/7697e84e-fd7f-47ae-97cf-66a266c9b4c0)

## Introduction

This bundle works with data fixtures in .yml format, detached from the common Doctrine DataFixtures.
There are multiple ways of loading fixture files.
This bundle offers loading Fixtures from .yml ,  dropping and recreating the ORM Schema.


## TestCases  ###

### Simple TestCase ###
Is an extension of PHPUnit_Framework_TestCase,   the simplest test case in PHPUnit

### Web Test Case ###
Is an extension of WebTestCase,  the functional test case in Symfony2 


## Installation

Installation with composer:

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

```yaml
# app/config/config_dev.yml

cosma_testing:
    fixture_path: Fixture                               # default
    entity_namespace: Entity                            # default
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