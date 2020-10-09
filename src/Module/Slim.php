<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Module;

use Codeception\Configuration;
use Codeception\Exception\ModuleConfigException;
use Codeception\Lib\Framework;
use Codeception\TestInterface;
use DoclerLabs\CodeceptionSlimModule\Lib\Connector\Slim as SlimConnector;
use Slim\App;

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

        $this->client = new SlimConnector();
        $this->client->setApp($this->app);

        parent::_before($test);
    }
}
