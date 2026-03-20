<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Test\Unit\Module;

use Codeception\Exception\ConfigurationException;
use Codeception\Exception\ModuleConfigException;
use Codeception\Lib\ModuleContainer;
use Codeception\Test\Unit;
use Codeception\TestInterface;
use DoclerLabs\CodeceptionSlimModule\Lib\Connector\SlimPsr7;
use DoclerLabs\CodeceptionSlimModule\Module\Slim;
use ReflectionProperty;
use Slim\App;

class SlimTest extends Unit
{
    public function testInitializeThrowsOnUnreadableApplicationPath(): void
    {
        $module = $this->createSlimModule(['application' => 'non/existent/path.php']);

        $this->expectException(ModuleConfigException::class);
        $this->expectExceptionMessage('Application file does not exist or is not readable');

        $module->_initialize();
    }

    public function testBeforeThrowsWhenAppNotReturned(): void
    {
        $fakePath = 'test/support/Fake/invalid_application.php';
        $module   = $this->createSlimModule(['application' => $fakePath]);
        $module->_initialize();

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Unable to bootstrap slim application');

        $module->_before($this->createMock(TestInterface::class));
    }

    public function testBeforeSetsAppAndClient(): void
    {
        $module = $this->createSlimModule(['application' => 'test/support/Fake/application.php']);
        $module->_initialize();

        $module->_before($this->createMock(TestInterface::class));

        $this->assertInstanceOf(App::class, $module->app);

        $clientProperty = new ReflectionProperty($module, 'client');
        $client         = $clientProperty->getValue($module);
        $this->assertInstanceOf(SlimPsr7::class, $client);
    }

    public function testBeforeAppliesConfigHeaders(): void
    {
        $headers = ['X-Custom' => 'value', 'Accept' => 'application/json'];
        $module  = $this->createSlimModule(
            [
                'application' => 'test/support/Fake/application.php',
                'headers'     => $headers,
            ]
        );
        $module->_initialize();

        $module->_before($this->createMock(TestInterface::class));

        $headersProperty = new ReflectionProperty($module, 'headers');
        $this->assertSame($headers, $headersProperty->getValue($module));
    }

    public function testBeforeAppliesEmptyHeadersByDefault(): void
    {
        $module = $this->createSlimModule(['application' => 'test/support/Fake/application.php']);
        $module->_initialize();

        $module->_before($this->createMock(TestInterface::class));

        $headersProperty = new ReflectionProperty($module, 'headers');
        $this->assertSame([], $headersProperty->getValue($module));
    }

    private function createSlimModule(array $config): Slim
    {
        $moduleContainer = $this->createMock(ModuleContainer::class);

        return new Slim($moduleContainer, $config);
    }
}
