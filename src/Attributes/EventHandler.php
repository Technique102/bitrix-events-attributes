<?php

declare(strict_types=1);

namespace Technique102\BitrixEventsAttributes\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class EventHandler
{
    public function __construct(
        private readonly string $moduleId,
        private readonly string $eventType
    ) {

    }

    public function add($class, $method): void
    {
        \Bitrix\Main\EventManager::getInstance()->addEventHandler($this->moduleId, $this->eventType, [$class, $method]);
    }
}
