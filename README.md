# DoclerLabs - Codeception Slim Module

[![Build Status](https://img.shields.io/github/workflow/status/DoclerLabs/codeception-slim-module/CI?label=build&style=flat-square)](https://github.com/DoclerLabs/codeception-slim-module/actions?query=workflow%3ACI)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)

This module allows you to run functional tests inside [Slim 4 Microframework](http://www.slimframework.com/docs/v4/) without HTTP calls,
so tests will be much faster and debug could be easier.

Inspiration comes from [herloct/codeception-slim-module](https://github.com/herloct/codeception-slim-module) library.

## Install

### Minimal requirements
- php: `^7.2`
- slim/slim: `^4.2`
- codeception/codeception: `^4.0`

If you don't know Codeception, please check [Quickstart Guide](https://codeception.com/quickstart) first.

If you already have [Codeception](https://github.com/Codeception/Codeception) installed in your Slim application,
you can add codeception-slim-module with a single composer command.

```shell
composer require --dev docler-labs/codeception-slim-module
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

### Connectors

Slim v4 comes with full PSR7 compatibility, so you are able to use any PSR7 implementation in your application.
This module comes with `slim/psr7` connector by default. If you use different implementation, you need to implement you own connector and feel free to contribute.

**Custom connector example**

```php

namespace Vendor\Package\SlimConnector;

use DoclerLabs\CodeceptionSlimModule\Lib\Connector\SlimConnectorInterface;
use Slim\App;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

class MyCustomConnector extends AbstractBrowser implements SlimConnectorInterface
{
    /** @var App */
    private $app;

    public function setApp(App $app): void
    {
        $this->app = $app;
    }

    /**
     * @param BrowserKitRequest $request An origin request instance.
     *
     * @return BrowserKitResponse An origin response instance.
     */
    protected function doRequest($request): BrowserKitResponse
    {
        $slimRequest = // ... convert BrowserKitRequest to your Slim Request object.

        $slimResponse = $this->app->handle($slimRequest);

        return new BrowserKitResponse(
            (string)$slimResponse->getBody(),
            $slimResponse->getStatusCode(),
            $slimResponse->getHeaders()
        );
    }
}
```

**Inject custom connector example (`test/suite/functional.suite.yml`)**
```yaml
modules:
  enabled:
    - REST:
        depends: DoclerLabs\CodeceptionSlimModule\Module\Slim

  config:
    DoclerLabs\CodeceptionSlimModule\Module\Slim:
      application: path/to/application.php
      connector: Vendor\Package\SlimConnector/MyCustomConnector
```

You can inject a custom connector with `connector` property.

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
