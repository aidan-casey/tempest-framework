<?php

declare(strict_types=1);

namespace Tempest\Bootstraps;

use Tempest\Application\Kernel;
use function Tempest\path;

final readonly class ConfigBootstrapper implements Bootstrapper
{
    public function __invoke(Kernel $kernel): void
    {
        // The expectation at this point, is that all our config files
        // have been discovered via attributes. What we are loading now
        // are overrides.
        $container = $kernel->getContainer();

        $configFiles = glob(path($kernel->getPath(), 'Config/**.php'));

        foreach ($configFiles as $configFile) {
            $configObject = require $configFile;

            $container->config($configObject);
        }
    }
}
