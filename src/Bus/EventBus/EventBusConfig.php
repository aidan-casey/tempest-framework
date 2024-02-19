<?php

namespace Tempest\Bus\EventBus;

final class EventBusConfig
{
    public function __construct(
        public readonly EventBusDriver $driver
    ) {}
}