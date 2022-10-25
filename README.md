# Symfony Forms in Detail

Repository contains example application used for "Practical Forms with Symfony" at [SymfonyCon Disneyland Paris 2022](https://live.symfony.com/2022-paris-con/)

## Requirements

* PHP >= 8.1
* [Doctrine compatible](https://www.doctrine-project.org/projects/doctrine-dbal/en/3.4/reference/introduction.html#introduction) database layer, eg. SQLite

## Setting up

**Checkout & Build** 

```bash
git clone git@github.com:chr-hertel/product-crud.git
cd product-crud
composer install
```

**Webserver**

Configure your vhost root to point to `public/` or use  

```bash
symfony serve --daemon
```

and open homepage (eg https://localhost:8000)

## Database

```bash
bin/console doctrine:database:create
bin/console doctrine:schema:create
```

## Quality Checks

You can execute the configured quality checks by running

```bash
bin/check
```

It will execute:

* Symfony Yaml- and Twig-Linting
* Doctrine Schema Validation
* Composer Validation
* PHPStan Static Code Analysis
* PHP-CS-Fixer Code Style
* PHPUnit Testing
