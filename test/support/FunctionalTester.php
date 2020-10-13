<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Test;

use Codeception\Actor;
use Codeception\Module\Asserts;
use Codeception\Module\REST;

/**
 * @mixin Asserts
 * @mixin REST
 */
class FunctionalTester extends Actor
{
    use _generated\FunctionalTesterActions;
}
