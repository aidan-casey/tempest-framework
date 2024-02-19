<?php

namespace Tempest\Bus\EventBus;

interface EventBus
{
    public function dispatch(object $event): void;

    public function subscribe(string $event, callable $handler): void;
}