# FlightPHP Container

[![Latest Stable Version](http://poser.pugx.org/flightphp/container/v?style=for-the-badge)](https://packagist.org/packages/flightphp/container)
[![Total Downloads](http://poser.pugx.org/flightphp/container/downloads?style=for-the-badge)](https://packagist.org/packages/flightphp/container)
[![License](http://poser.pugx.org/flightphp/container/license?style=for-the-badge)](https://packagist.org/packages/flightphp/container)
[![PHP Version Require](http://poser.pugx.org/flightphp/container/require/php?style=for-the-badge)](https://packagist.org/packages/flightphp/container)
![phpstan](https://img.shields.io/badge/phpstan-max-green?style=for-the-badge)
![code coverage](https://img.shields.io/badge/code_coverage-100%25-green?style=for-the-badge)

A lightweight Dependency Injection Container (DIC) for PHP, to manage and streamline object dependencies effectively.

## Features

- **Lightweight and Efficient:** Manage dependencies with ease.
- **Flexible Configuration:** Easily integrate with various PHP applications.
- **PSR-11 Compliance:** Ensures interoperability between containers.

## Requirements

- PHP 7.4 or higher
- Composer installed on your system

## Installation

To include the FlightPHP Container in your project, you can use Composer:

```bash
composer require flightphp/container
```

## Simple usage

To use the FlightPHP Container, you can create a new instance of the container and bind your dependencies:

```php
<?php

require 'vendor/autoload.php';

use flight\Container;

$container = new Container;

$container->set(PDO::class, fn(): PDO => new PDO(
  'mysql:host=localhost;dbname',
  'username',
  'password'
));

$pdo = $container->get(PDO::class);

var_dump($pdo);

/*
object(PDO)#3 (0) {
}
 */
```

## Usage in FlightPHP Framework

You can use the FlightPHP Container in your FlightPHP application by setting the container instance:

```php
<?php

require 'vendor/autoload.php';

use flight\Container;

$container = new Container;

$container->set(PDO::class, fn(): PDO => new PDO('sqlite::memory:'));

Flight::registerContainerHandler([$container, 'get']);

class TestController {
  private PDO $pdo;

  function __construct(PDO $pdo) {
    $this->pdo = $pdo;
  }

  function index() {
    var_dump($this->pdo);
  }
}

Flight::route('GET /', [TestController::class, 'index']);

Flight::start();
```

Go and visit that route in your dev server and you'll see something like:

```
object(PDO)#3 (0) {
}
```

## Advance usage

FlightPHP Container can resolve dependencies recursively, allowing you to bind complex objects and dependencies:

```php
<?php

require 'vendor/autoload.php';

use flight\Container;

class User {}

interface UserRepository {
  function find(int $id): ?User;
}

class PdoUserRepository implements UserRepository {
  private PDO $pdo;

  function __construct(PDO $pdo) {
    $this->pdo = $pdo;
  }

  function find(int $id): ?User {
    // Implementation ...
    return null;
  }
}

$container = new Container;

$container->set(PDO::class, static fn(): PDO => new PDO('sqlite::memory:'));
$container->set(UserRepository::class, PdoUserRepository::class);

$userRepository = $container->get(UserRepository::class);
var_dump($userRepository);

/*
object(PdoUserRepository)#4 (1) {
  ["pdo":"PdoUserRepository":private]=>
  object(PDO)#3 (0) {
  }
}
 */
```

## License

FlightPHP Container is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
