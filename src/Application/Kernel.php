<?php

declare(strict_types=1);

namespace Tempest\Application;

use Tempest\Container\Container;

interface Kernel
{
    public function getContainer(): Container;

    public function setContainer(Container $container): void;

    public function getPath(): string;

    public function setPath(string $path): void;

    public function run(): void;
}
