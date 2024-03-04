<?php

declare(strict_types=1);

namespace Tempest\Bootstraps;

use AppendIterator;
use Generator;
use Iterator;
use ReflectionClass;
use Tempest\Application\Kernel;
use Tempest\Discovery\ConfigDiscovery;
use Tempest\Discovery\DiscoveryDiscovery;
use Tempest\Discovery\DiscoveryService;
use function Tempest\path;

final readonly class DiscoveryBootstrapper implements Bootstrapper
{
    public function __invoke(Kernel $kernel): void
    {
        $container = $kernel->getContainer();
        $container->singleton(DiscoveryService::class, fn () => new DiscoveryService());

        $discovery = $container->get(DiscoveryService::class);

        $discovery->addDiscoverer($container->get(ConfigDiscovery::class));

        foreach ($this->discoverClasses($kernel->getPath()) as $class) {
            $discovery->addClass($class);
        }

        $discovery->addDiscoverer(
            $container->get(DiscoveryDiscovery::class)
        );

        $discovery->discover();
    }

    private function discoverClasses(string $root): Generator
    {
        $classes = require path($root . '/vendor/composer/autoload_classmap.php');

        foreach ($this->discoverSearchNamespaces($root) as $namespace) {
            foreach ($classes as $class => $file) {
                if (! str_starts_with($class, $namespace)) {
                    continue;
                }

                yield new ReflectionClass($class);
            }
        }
    }

    private function discoverSearchNamespaces(string $root): Iterator
    {
        $iterator = new AppendIterator();

        $iterator->append($this->discoverAppNamespaces($root));
        $iterator->append($this->discoverPackageNamespaces($root));

        return $iterator;
    }

    private function discoverAppNamespaces(string $root): Generator
    {
        $composer = $this->loadJsonFile(path($root, 'composer.json'));

        foreach ($composer['autoload']['psr-4'] ?? [] as $namespace => $file) {
            yield $namespace;
        }

        foreach ($composer['autoload']['psr-0'] ?? [] as $namespace => $file) {
            yield $namespace;
        }

        // Discover everything Tempest.
        yield 'Tempest\\';
    }

    private function discoverPackageNamespaces(string $root): Generator
    {
        $composer = $this->loadJsonFile(path($root, 'vendor/composer/installed.json'));
        $packages = $composer['packages'];

        foreach ($packages as $package) {
            $requiresTempest = (
                isset($package['require']['tempest/framework']) ||
                isset($package['require-dev']['tempest/framework'])
            );

            if (! $requiresTempest) {
                continue;
            }

            foreach ($package['autoload']['psr-4'] ?? [] as $namespace => $file) {
                yield $namespace;
            }

            foreach ($package['autoload']['psr-0'] ?? [] as $namespace => $file) {
                yield $namespace;
            }
        }
    }

    private function loadJsonFile(string $file): array
    {
        if (! is_file($file)) {
            throw new BootstrapException(sprintf('Could not locate %s, try running "composer install"', $file));
        }

        return json_decode(file_get_contents($file), true);
    }
}
