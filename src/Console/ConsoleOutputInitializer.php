<?php

declare(strict_types=1);

namespace Tempest\Console;

use Tempest\Application\ConsoleKernel;
use Tempest\Application\Kernel;
use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;

#[Singleton]
final readonly class ConsoleOutputInitializer implements Initializer
{
    public function initialize(Container $container): ConsoleOutput
    {
        $app = $container->get(Kernel::class);

        if (! $app instanceof ConsoleKernel) {
            $consoleOutput = new NullConsoleOutput();
        } else {
            $consoleOutput = new GenericConsoleOutput();
        }

        return $consoleOutput;
    }
}
