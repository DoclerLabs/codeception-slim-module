<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Module;

use Codeception\Configuration;
use Codeception\Exception\ConfigurationException;
use Codeception\Exception\ModuleConfigException;
use Codeception\Lib\Framework;
use Codeception\TestInterface;
use DoclerLabs\CodeceptionSlimModule\Lib\Connector\SlimConnectorInterface;
use DoclerLabs\CodeceptionSlimModule\Lib\Connector\SlimPsr7;
use Slim\App;
use Symfony\Component\BrowserKit\AbstractBrowser;

/**
 * This module uses Slim App to emulate requests and test response.
 *
 * ## Configuration
 *
 * ### Slim 4.x
 *
 * * application          - Relative path to file which bootstrap and returns your `Slim\App` instance.
 * * connector *optional* - Connector class.
 *                          You can use any PSR7 implementation in your application, just need to implement proper connector.
 *                          Default: DoclerLabs\CodeceptionSlimModule\Lib\Connector\SlimPsr7, that
 *                          implements request and response mapping between Symfony BrowserKit and slim/psr7 library.
 *
 * #### Example (`test/suite/functional.suite.yml`)
 * ```yaml
 * modules:
 *   config:
 *     DoclerLabs\CodeceptionSlimModule\Module\Slim:
 *       application: 'app/bootstrap.php'
 *       connector: 'Vendor/Package/CustomConnectorImplementation'
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

    /** @var string[] */
    protected $config = [
        'connector' => SlimPsr7::class,
    ];

    /** @var array */
    protected $requiredFields = ['application'];

    /** @var string */
    private $applicationPath;

    /** @var string */
    private $connectorClass;

    public function _initialize(): void
    {
        $applicationPath = Configuration::projectDir() . $this->config['application'];
        if (!file_exists($applicationPath)) {
            throw new ModuleConfigException(
                static::class,
                sprintf(
                    "\nApplication file doesn't exist.\nPlease, check path for php file: `%s`",
                    $applicationPath
                )
            );
        }

        $connectorClass = $this->config['connector'];
        if (!class_exists($connectorClass)) {
            throw new ModuleConfigException(
                static::class,
                sprintf(
                    "\nUnable to load `%s` connector.\nPlease, check connector configuration.",
                    $connectorClass
                )
            );
        }

        $this->applicationPath = $applicationPath;
        $this->connectorClass  = $connectorClass;

        parent::_initialize();
    }

    public function _before(TestInterface $test): void
    {
        /** @noinspection PhpIncludeInspection */
        $this->app = require $this->applicationPath;

        // Check if app instance is ready.
        if (!$this->app instanceof App) {
            throw new ConfigurationException(
                sprintf(
                    "\n  Unable to bootstrap slim application.\n  Application file must return with `%s` instance.",
                    App::class
                )
            );
        }

        // Check connector requirements.
        $connector = new $this->connectorClass();
        if (!$connector instanceof AbstractBrowser) {
            throw new ConfigurationException(
                sprintf(
                    "\n  Unable to load `%s` connector.\n  Connector must extend `%s`.",
                    $this->connectorClass,
                    AbstractBrowser::class
                )
            );
        }

        if (!$connector instanceof SlimConnectorInterface) {
            throw new ConfigurationException(
                sprintf(
                    "\n  Unable to load `%s` connector.\n  Connector must implement `%s`.",
                    $this->connectorClass,
                    SlimConnectorInterface::class
                )
            );
        }

        $connector->setApp($this->app);

        $this->client = $connector;

        parent::_before($test);
    }
}
