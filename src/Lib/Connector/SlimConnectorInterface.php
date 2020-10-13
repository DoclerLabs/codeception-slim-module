<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Lib\Connector;

use Slim\App;

interface SlimConnectorInterface
{
    public function setApp(App $app): void;
}
