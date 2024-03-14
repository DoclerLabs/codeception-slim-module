# DoclerLabs - Codeception Slim Module

[![Build Status](https://img.shields.io/github/workflow/status/DoclerLabs/codeception-slim-module/CI?label=build&style=flat-square)](https://github.com/DoclerLabs/codeception-slim-module/actions?query=workflow%3ACI)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)

This module allows you to run functional tests inside [Slim 4 Microframework](http://www.slimframework.com/docs/v4/) without HTTP calls,
so tests will be much faster and debug could be easier.

Inspiration comes from [herloct/codeception-slim-module](https://github.com/herloct/codeception-slim-module) library.

## Install

### Minimal requirements
- php: `^8.0`
- slim/slim: `^4.7`
- codeception/codeception: `^5.0`

If you don't know Codeception, please check [Quickstart Guide](https://codeception.com/quickstart) first.

If you already have [Codeception](https://github.com/Codeception/Codeception) installed in your Slim application,
you can add codeception-slim-module with a single composer command.

```shell
composer require --dev docler-labs/codeception-slim-module
```

For PHP 7 support, please use `docler-labs/codeception-slim-module:^2.0` version

```shell
composer require --dev docler-labs/codeception-slim-module "^2.0"
```

If you use Slim v3, please use the previous version from library:

```shell
composer require --dev docler-labs/codeception-slim-module "^1.0"
```

### Configuration

**Example (`test/suite/functional.suite.yml`)**
```yaml
modules:
  enabled:
    - REST:
        depends: DoclerLabs\CodeceptionSlimModule\Module\Slim

  config:
    DoclerLabs\CodeceptionSlimModule\Module\Slim:
      application: path/to/application.php
```

The `application` property is a relative path to file which returns your `Slim\App` instance.
Here is the minimum `application.php` content:

```php
require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Add routes and middlewares here.

return $app;
```

## Testing your API endpoints

```php

class UserCest
{
    public function getUserReturnsWithEmail(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET('/users/John');

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(
            [
                'email' => 'john.doe@example.com',
            ]
        );
    }
}
```
