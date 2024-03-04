<?php

declare(strict_types=1);

namespace Tempest\Database;

use Tempest\Application\Config;
use Tempest\Database\Drivers\SQLiteDriver;

#[Config]
final class DatabaseConfig
{
    public function __construct(
        public readonly DatabaseDriver $driver = new SQLiteDriver(
            path: __DIR__ . '/database.sqlite'
        ),
        public array $migrations = [],
    ) {
    }

    public function addMigration(string $className): self
    {
        $this->migrations[] = $className;

        return $this;
    }
}
