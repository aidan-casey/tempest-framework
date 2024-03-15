<?php

declare(strict_types=1);

namespace Tempest\Filesystem\Local;

use Tempest\Filesystem\Driver;
use Tempest\Filesystem\Exception\UnableToCreateDirectory;
use Tempest\Filesystem\Exception\UnableToDeleteDirectory;
use Tempest\Filesystem\Exception\UnableToReadFile;
use Tempest\Filesystem\Exception\UnableToWriteFile;
use Tempest\Filesystem\Stream;

final class LocalDriver implements Driver
{
    public function read(string $location): string
    {
        error_clear_last();

        $contents = @file_get_contents($location);

        if ($contents === false) {
            throw UnableToReadFile::fromLocation(
                location: $location,
                because: error_get_last()['message'] ?? ''
            );
        }

        return $contents;
    }

    public function write(string $location, string $content): void
    {
        error_clear_last();

        if (@file_put_contents($location, $content) === false) {
            throw UnableToWriteFile::atLocation(
                location: $location,
                because: error_get_last()['message'] ?? ''
            );
        }
    }

    public function isFile(string $location): bool
    {
        return is_file($location);
    }

    public function isDirectory(string $location): bool
    {
        return is_dir($location);
    }

    public function createDirectory(string $location, int $permissions = 0777): void
    {
        error_clear_last();

        $directoryWasCreated = @mkdir(
            directory: $location,
            permissions: $permissions,
            recursive: true
        );

        if ($directoryWasCreated) {
            return;
        }

        throw UnableToCreateDirectory::atLocation(
            location: $location,
            because: error_get_last()['message'] ?? ''
        );
    }

    public function deleteDirectory(string $location): void
    {
        error_clear_last();

        $directoryWasDeleted = @rmdir($location);

        if ($directoryWasDeleted) {
            return;
        }

        throw UnableToDeleteDirectory::atLocation(
            location: $location,
            because: error_get_last()['message'] ?? ''
        );
    }

    public function createStream(string $location): Stream
    {
        return new LocalStream($location);
    }
}
