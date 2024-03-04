<?php

declare(strict_types=1);

namespace Tempest\Bootstraps;

use Tempest\Application\Kernel;

interface Bootstrapper
{
    public function __invoke(Kernel $kernel): void;
}
