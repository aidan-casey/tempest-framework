<?php

namespace Tempest\Discovery;

use ReflectionClass;
use Tempest\Application\Config;
use Tempest\Container\Container;
use function Tempest\attribute;

class ConfigDiscovery implements Discovery
{
    private const CACHE_PATH = __DIR__ . '/config-discovery.cache.php';

    public function __construct(private Container $container)
    {
    }

    public function discover(ReflectionClass $class): void
    {
        if (attribute(Config::class)->in($class)->first()) {
            $this->container->config(
                $this->container->get($class->getName())
            );
        }
    }

    public function hasCache(): bool
    {
        return file_exists(self::CACHE_PATH);
    }

    public function storeCache(): void
    {
        //
    }

    public function restoreCache(Container $container): void
    {
        //
    }

    public function destroyCache(): void
    {
        @unlink(self::CACHE_PATH);
    }
}