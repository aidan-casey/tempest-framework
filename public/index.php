<?php

use Tempest\Container\TempestContainer;
use Tempest\Tempest;

require_once __DIR__ . '/../vendor/autoload.php';

$container = TempestContainer::getInstance();

Tempest::http(container: $container, path: __DIR__ . '/../')->run();

exit;
