<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Module;

use Codeception\Configuration;
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
 * * application: 'app/bootstrap.php' - relative path to file which bootstrap and returns your `Slim\App` instance.
 *
 * #### Example (`test/suite/functional.suite.yml`)
 * ```yaml
 * modules:
 *   enabled:
 *     - DoclerLabs\CodeceptionSlimModule\Module\Slim:
 *         application: 'app/bootstrap.php'
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
 *     - DoclerLabs\CodeceptionSlimModule\Module\Slim:
 *         application: 'app/bootstrap.php'
 *     - REST:
 *         depends: DoclerLabs\CodeceptionSlimModule\Module\Slim
 * ```
 */
class Slim extends Framework
{
    /** @var App */
    public $app;

    /** @var array */
    protected $requiredFields = ['application'];

    /** @var string */
    private $applicationPath;

    public function _initialize(): void
    {
        $this->applicationPath = Configuration::projectDir() . $this->config['application'];

        if (!file_exists($this->applicationPath)) {
            throw new ModuleConfigException(
                static::class,
                "\nApplication file doesn't exist.\n"
                . 'Please, check path for php file: ' . $this->applicationPath
            );
        }

        parent::_initialize();
    }

    public function _before(TestInterface $test): void
    {
        $this->app = require $this->applicationPath;

        $this->client = new SlimPsr7();
        $this->client->setApp($this->app);

        parent::_before($test);
    }
}
