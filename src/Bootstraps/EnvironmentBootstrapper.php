<?php

declare(strict_types=1);

namespace Tempest\Bootstraps;

use Dotenv\Dotenv;
use Tempest\Application\Kernel;

final class EnvironmentBootstrapper implements Bootstrapper
{
    public function __invoke(Kernel $kernel): void
    {
        $dotEnv = Dotenv::createUnsafeImmutable($kernel->getPath());

        $dotEnv->safeLoad();
    }
}
