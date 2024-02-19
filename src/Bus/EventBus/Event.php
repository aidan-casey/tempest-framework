<?php

namespace Tempest\Bus\EventBus;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Event
{
    public function __construct(public ?string $alias = null)
    {}
}