<?php

declare(strict_types=1);

namespace Tempest;

use Tempest\Application\Config;
use Tempest\Application\Environment;
use Tempest\Discovery\DiscoveryDiscovery;

#[Config]
final class AppConfig
{
    public function __construct(
        public Environment $environment = Environment::LOCAL,
        public bool $discoveryCache = false,

        public array $discoveryLocations = [],

        /** @var class-string[] */
        public array $discoveryClasses = [
            DiscoveryDiscovery::class,
        ],

        /** @var \Tempest\Exceptions\ExceptionHandler[] */
        public array $exceptionHandlers = [],
        public bool $enableExceptionHandling = true,
    ) {
    }
}
