# DoclerLabs - Codeception Slim Module

This module allows you to run tests inside [Slim 3 Microframework](http://www.slimframework.com/docs/v3/).
Inspiration comes from [herloct/codeception-slim-module](https://github.com/herloct/codeception-slim-module) repository.

## Development

Install
```shell
composer install
```

Run tests
```shell
vendor/bin/codecept run
```

## Install

```shell
composer require --dev docler-labs/codeception-slim-module
```

## Config

### Slim v3

**Example (`test/suite/functional.suite.yml`)**
```yaml
modules:
  enabled:
    - DoclerLabs\CodeceptionSlimModule\Module\Slim:
        application: path/to/application.php
    - REST:
        depends: DoclerLabs\CodeceptionSlimModule\Module\Slim
```

The `application` property is a relative path to file which returns your App.
Here is the minimum `application.php` content.

```php
require __DIR__ . '/vendor/autoload.php';

use Slim\App;

$app = new App();

// Add routes and middlewares here.

return $app;
```
