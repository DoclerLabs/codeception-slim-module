<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Module;

use Codeception\Configuration;
use Codeception\Exception\ConfigurationException;
use Codeception\Exception\ModuleConfigException;
use Codeception\Lib\Framework;
use Codeception\TestInterface;
use DoclerLabs\CodeceptionSlimModule\Lib\Connector\SlimPsr7;
use Psr\Container\ContainerInterface;
use Slim\App;

/**
 * This module uses Slim App to emulate requests and test response.
 *
 * ## Configuration
 *
 * ### Slim 4.x
 *
 * * application - Relative path to file which bootstrap and returns your `Slim\App` instance.
 * * headers - Default headers to be sent with every request (optional).
 *
 * #### Example (`test/suite/functional.suite.yml`)
 * ```yaml
 * modules:
 *   config:
 *     DoclerLabs\CodeceptionSlimModule\Module\Slim:
 *       application: 'app/bootstrap.php'
 *       headers:
 *         Content-Type: application/json
 * ```
 *
 * ## Public Properties
 *
 * * app - Slim App instance
 *
 * Usage example:
 *
 * ```yaml
 * actor: FunctionalTester
 * modules:
 *   enabled:
 *     - REST:
 *         depends: DoclerLabs\CodeceptionSlimModule\Module\Slim
 *
 *   config:
 *     DoclerLabs\CodeceptionSlimModule\Module\Slim:
 *       application: 'app/bootstrap.php'
 *       headers:
 *         Content-Type: application/json
 * ```
 */
class Slim extends Framework
{
    /** @var App<ContainerInterface|null> */
    public App $app;

    protected array $requiredFields = ['application'];

    protected array $config = ['headers' => []];

    private string $applicationPath;

    public function _initialize(): void
    {
        /** @var string $configApplication */
        $configApplication = $this->config['application'];
        $applicationPath   = Configuration::projectDir() . $configApplication;
        if (!is_readable($applicationPath)) {
            throw new ModuleConfigException(
                static::class,
                "Application file does not exist or is not readable.\nPlease, check path for php file: `$applicationPath`"
            );
        }

        $this->applicationPath = $applicationPath;

        parent::_initialize();
    }

    public function _before(TestInterface $test): void
    {
        $app = require $this->applicationPath;

        // Check if app instance is ready.
        if (!$app instanceof App) {
            throw new ConfigurationException(
                sprintf(
                    "Unable to bootstrap slim application.\n  Application file must return with `%s` instance.",
                    App::class
                )
            );
        }

        $this->app = $app;

        $connector = new SlimPsr7();
        $connector->setApp($this->app);

        $this->client = $connector;

        /** @var array<string, string> $headers */
        $headers       = $this->config['headers'];
        $this->headers = $headers;

        parent::_before($test);
    }
}
