<?php

declare(strict_types=1);

namespace Technique102\BitrixEventsAttributes;

use ReflectionClass;
use Technique102\BitrixEventsAttributes\Attributes\EventHandler;

final class EventManager
{
    private static ?EventManager $instance = null;

    protected bool $isBooted = false;

    private array $classContainer = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {

    }

    /**
     * @throws \ReflectionException
     */
    public function boot(): void
    {
        if ($this->isBooted === true)
            return;

        foreach ($this->classContainer as $class) {
            $reflectionClass = new ReflectionClass($class);
            foreach ($reflectionClass->getMethods() as $method) {
                $attributes = $method->getAttributes(EventHandler::class);
                foreach ($attributes as $attribute) {
                    $listener = $attribute->newInstance();
                    $listener->add($reflectionClass->getName(), $method->getName());
                }
            }
        }

        $this->isBooted = true;
    }

    public function addEventHandlerClass(string $class): void
    {
        if (!in_array($class, $this->classContainer))
            $this->classContainer[] = $class;
    }
}
