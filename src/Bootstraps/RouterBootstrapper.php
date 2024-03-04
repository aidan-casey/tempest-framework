<?php

namespace Tempest\Bootstraps;

use Tempest\Application\Kernel;
use Tempest\Http\RequestInitializer;

class RouterBootstrapper implements Bootstrapper
{
    public function __invoke(Kernel $kernel): void
    {
        $kernel->getContainer()->addInitializer(RequestInitializer::class);
    }
}