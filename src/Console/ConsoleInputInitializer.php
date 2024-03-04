<?php

declare(strict_types=1);

namespace Tempest\Console;

use Tempest\Application\ConsoleKernel;
use Tempest\Application\Kernel;
use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;

#[Singleton]
final readonly class ConsoleInputInitializer implements Initializer
{
    public function initialize(Container $container): ConsoleInput
    {
        $app = $container->get(Kernel::class);

        if (! $app instanceof ConsoleKernel) {
            $consoleInput = new NullConsoleInput();
        } else {
            $consoleInput = new GenericConsoleInput($container->get(ConsoleOutput::class));
        }

        return $consoleInput;
    }
}
