# DoclerLabs - Codeception Slim Module

[![Build Status](https://img.shields.io/github/workflow/status/DoclerLabs/codeception-slim-module/CI?label=build&style=flat-square)](https://github.com/DoclerLabs/codeception-slim-module/actions?query=workflow%3ACI)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)

This module allows you to run functional tests inside [Slim 3 Microframework](http://www.slimframework.com/docs/v3/) without HTTP calls,
so tests will be much faster and debug could be easier.

Inspiration comes from [herloct/codeception-slim-module](https://github.com/herloct/codeception-slim-module) library.

## Install

### Minimal requirements
- php: `^7.2 || ^8.0`
- slim/slim: `^3.1`
- codeception/codeception: `^4.0`

If you don't know Codeception, please check [Quickstart Guide](https://codeception.com/quickstart) first.

If you already have [Codeception](https://github.com/Codeception/Codeception) installed in your Slim application,
you can add codeception-slim-module with a single composer command.

```shell
composer require --dev docler-labs/codeception-slim-module
```

### Configuration

**Example (`test/suite/functional.suite.yml`)**
```yaml
modules:
  enabled:
    - DoclerLabs\CodeceptionSlimModule\Module\Slim:
        application: path/to/application.php
    - REST:
        depends: DoclerLabs\CodeceptionSlimModule\Module\Slim
```

The `application` property is a relative path to file which returns your `Slim\App` instance.
Here is the minimum `application.php` content:

```php
require __DIR__ . '/vendor/autoload.php';

use Slim\App;

$app = new App();

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
