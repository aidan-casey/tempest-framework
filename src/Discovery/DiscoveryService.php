<?php

namespace Tempest\Discovery;

use ReflectionClass;

final class DiscoveryService
{
    private array $classes = [];

    private array $discoverers = [];

    public function addClass(ReflectionClass|string $class): void
    {
        $class = is_string($class) ? new ReflectionClass($class): $class;

        $this->classes[$class->getName()] = $class;
    }

    public function addDiscoverer(Discovery $discovery): void
    {
        $this->discoverers[] = $discovery;
    }

    public function discover(): void
    {
        reset($this->discoverers);

        /**
         * @var Discovery $discoveryClass
         */
        while ($discoveryClass = current($this->discoverers)) {
            foreach ($this->classes as $class) {
                $discoveryClass->discover($class);
            }

            next($this->discoverers);
        }
    }
}