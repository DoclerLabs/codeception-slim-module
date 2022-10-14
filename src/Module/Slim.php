<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Module;

use Codeception\Configuration;
use Codeception\Exception\ConfigurationException;
use Codeception\Exception\ModuleConfigException;
use Codeception\Lib\Framework;
use Codeception\TestInterface;
use DoclerLabs\CodeceptionSlimModule\Lib\Connector\SlimPsr7;
use Slim\App;

/**
 * This module uses Slim App to emulate requests and test response.
 *
 * ## Configuration
 *
 * ### Slim 4.x
 *
 * * application - Relative path to file which bootstrap and returns your `Slim\App` instance.
 *
 * #### Example (`test/suite/functional.suite.yml`)
 * ```yaml
 * modules:
 *   config:
 *     DoclerLabs\CodeceptionSlimModule\Module\Slim:
 *       application: 'app/bootstrap.php'
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
 * ```
 */
class Slim extends Framework
{
    /** @var App */
    public $app;

    /** @var array */
    protected array $requiredFields = ['application'];

    /** @var string */
    private $applicationPath;

    public function _initialize(): void
    {
        $applicationPath = Configuration::projectDir() . $this->config['application'];
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
        /* @noinspection PhpIncludeInspection */
        $this->app = require $this->applicationPath;

        // Check if app instance is ready.
        if (!$this->app instanceof App) {
            throw new ConfigurationException(
                sprintf(
                    "Unable to bootstrap slim application.\n  Application file must return with `%s` instance.",
                    App::class
                )
            );
        }

        $connector = new SlimPsr7();
        $connector->setApp($this->app);

        $this->client = $connector;

        parent::_before($test);
    }
}
