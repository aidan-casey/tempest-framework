<?php

declare(strict_types=1);

namespace Tempest;

use Tempest\Application\ConsoleKernel;
use Tempest\Application\HttpKernel;
use Tempest\Application\Kernel;
use Tempest\Container\Container;

final readonly class Tempest
{
    public static function http(Container $container, ?string $path = null): Kernel
    {
        $path ??= getcwd();

        return new HttpKernel($container, $path);
    }

    public static function console(Container $container, ?string $path = null): Kernel
    {
        $path ??= getcwd();

        return new ConsoleKernel($container, $path);
    }
}
