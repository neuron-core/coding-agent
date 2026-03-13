<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Registry;

use function array_keys;

/**
 * Registry for event handlers that extensions can register.
 */
final class EventRegistry
{
    /** @var array<string, array<int, callable>> */
    private array $handlers = [];

    /**
     * Register a callback for a specific event.
     */
    public function register(string $event, callable $handler): void
    {
        $this->handlers[$event][] = $handler;
    }

    /**
     * Get all handlers for a specific event.
     *
     * @return array<int, callable>
     */
    public function handlersFor(string $event): array
    {
        return $this->handlers[$event] ?? [];
    }

    /**
     * Check if any handlers are registered for an event.
     */
    public function has(string $event): bool
    {
        return isset($this->handlers[$event]) && !empty($this->handlers[$event]);
    }

    /**
     * Get all registered event names.
     *
     * @return string[]
     */
    public function registeredEvents(): array
    {
        return array_keys($this->handlers);
    }

    /**
     * Clear all handlers for a specific event.
     */
    public function clear(string $event): void
    {
        unset($this->handlers[$event]);
    }
}
