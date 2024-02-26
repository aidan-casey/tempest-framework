<?php

namespace Tempest\Testing\Console;

use Tempest\AppConfig;
use Tempest\Application\ConsoleApplication;
use Tempest\Console\ConsoleOutput;
use Tempest\Console\GenericConsoleFormatter;
use Tempest\Container\Container;

final class ConsoleCommandTester
{
    public function __construct(private Container $container)
    {
        $this->container->singleton(
            ConsoleOutput::class,
            fn () => $this->container->get(TestConsoleOutput::class)
        );
    }

    public function call(string $command): TestConsoleHelper
    {
        $application = new ConsoleApplication(
            args: ['tempest', ...explode(' ', $command)],
            container: $this->container,
            appConfig: $this->container->get(AppConfig::class)
        );

        $application->run();

        return new TestConsoleHelper(
            $this->container->get(ConsoleOutput::class),
            $this->container->get(GenericConsoleFormatter::class)
        );
    }
}