# Symfony Forms in Detail

Repository contains example application used for "Practical Forms with Symfony" at [SymfonyCon Amsterdam 2019](https://amsterdam2019.symfony.com/)

## Requirements

* PHP >= 7.1.3
* [Doctrine compatible](https://www.doctrine-project.org/projects/doctrine-dbal/en/2.9/reference/introduction.html#introduction) database layer, eg. SQLite

## Setting up

**Checkout & Build** 

```bash
$ git clone git@github.com:chr-hertel/product-crud.git
$ cd product-crud
$ composer install
```

**Webserver**

Configure your vhost root to point to `public/` or use  

```bash
$ bin/console server:start
```

and open homepage (eg http://localhost:8000)

## Database

```bash
$ bin/console doctrine:database:create
$ bin/console doctrine:schema:create --force
```

## Quality Checks

You can execute the configured quality checks by running

```bash
$ bin/check
```

It will execute:

* Symfony Yaml- and Twig-Linting
* Doctrine Schema Validation
* Composer Validation
* PHPStan Static Code Analysis
* PHP-CS-Fixer Code Style
* Security Checker
* PHPUnit Testing
