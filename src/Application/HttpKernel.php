<?php

declare(strict_types=1);

namespace Tempest\Application;

use Tempest\Bootstraps\Bootstrapper;
use Tempest\Bootstraps\ConfigBootstrapper;
use Tempest\Bootstraps\DiscoveryBootstrapper;
use Tempest\Bootstraps\EnvironmentBootstrapper;
use Tempest\Bootstraps\RouterBootstrapper;
use Tempest\Container\Container;
use Tempest\Http\Request;
use Tempest\Http\ResponseSender;
use Tempest\Http\Router;

final class HttpKernel implements Kernel
{
    /**
     * This contains an array of bootstrappers that
     *  will set up our application to run.
     *
     * @var Bootstrapper[]
     */
    private array $bootstrappers = [
        EnvironmentBootstrapper::class,
        DiscoveryBootstrapper::class,
        ConfigBootstrapper::class,
        RouterBootstrapper::class,
    ];

    private bool $hasBooted = false;

    private Container $container;

    private string $path;

    public function __construct(Container $container, string $path)
    {
        $this->setContainer($container);
        $this->setPath($path);
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
        $this->path = realpath($path);
    }

    public function run(): void
    {
        // If we have yet to boot, go ahead and do that.
        $this->boot();

        // Now we go ahead and run our application.
        // TODO: Exception handler
        $router = $this->container->get(Router::class);
        $request = $this->container->get(Request::class);
        $responseSender = $this->container->get(ResponseSender::class);

        $responseSender->send(
            $router->dispatch($request),
        );
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
}
