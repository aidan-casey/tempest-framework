<?php

declare(strict_types=1);

namespace Tempest\Application;

use ArgumentCountError;
use ReflectionMethod;
use Tempest\Bootstraps\ConfigBootstrapper;
use Tempest\Bootstraps\DiscoveryBootstrapper;
use Tempest\Bootstraps\EnvironmentBootstrapper;
use Tempest\Console\ConsoleConfig;
use Tempest\Console\ConsoleOutput;
use Tempest\Console\RenderConsoleCommandOverview;
use Tempest\Container\Container;

final class ConsoleKernel implements Kernel
{
    private array $args;

    private array $bootstrappers = [
        EnvironmentBootstrapper::class,
        DiscoveryBootstrapper::class,
        ConfigBootstrapper::class,
    ];

    private bool $hasBooted = false;

    private Container $container;

    private string $path;

    public function __construct(Container $container, string $path)
    {
        $this->setContainer($container);
        $this->setPath($path);

        $this->args = $_SERVER['argv'];
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function setContainer(Container $container): void
    {
        $this->container = $container;

        $this->container->singleton(Kernel::class, fn () => $this);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function run(): void
    {
        $this->boot();

        $commandName = $this->args[1] ?? null;

        $output = $this->container->get(ConsoleOutput::class);

        if (! $commandName) {
            $output->writeln($this->container->get(RenderConsoleCommandOverview::class)());

            return;
        }

        $this->handleCommand($commandName);
    }

    private function boot(): void
    {
        if ($this->hasBooted) {
            return;
        }

        foreach ($this->bootstrappers as $bootstrapper) {
            $bootstrapper = new $bootstrapper;
            $bootstrapper($this);
        }
    }

    private function handleCommand(string $commandName): void
    {
        $config = $this->container->get(ConsoleConfig::class);

        $consoleCommandConfig = $config->commands[$commandName] ?? null;

        if (! $consoleCommandConfig) {
            throw new CommandNotFound($commandName);
        }

        $handler = $consoleCommandConfig->handler;

        $params = $this->resolveParameters($handler);

        $commandClass = $this->container->get($handler->getDeclaringClass()->getName());

        try {
            $handler->invoke($commandClass, ...$params);
        } catch (ArgumentCountError) {
            $this->handleFailingCommand();
        }
    }

    private function resolveParameters(ReflectionMethod $handler): array
    {
        $parameters = $handler->getParameters();
        $inputArguments = $this->args;
        unset($inputArguments[0], $inputArguments[1]);
        $inputArguments = array_values($inputArguments);

        $result = [];

        foreach ($inputArguments as $i => $argument) {
            if (str_starts_with($argument, '--')) {
                $parts = explode('=', str_replace('--', '', $argument));

                $key = $parts[0];

                $result[$key] = $parts[1] ?? true;
            } else {
                $key = ($parameters[$i] ?? null)?->getName();

                $result[$key ?? $i] = $argument;
            }
        }

        return $result;
    }

    private function handleFailingCommand(): void
    {
        $output = $this->container->get(ConsoleOutput::class);

        $output->error('Something went wrong');
    }
}
