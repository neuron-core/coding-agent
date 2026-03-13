<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Registry;

use NeuronCore\Maestro\Rendering\ToolRenderer;

use function array_keys;

/**
 * Registry for tool renderers that extensions can register.
 */
final class RendererRegistry
{
    /** @var array<string, ToolRenderer> */
    private array $map = [];

    public function __construct(
        private readonly ToolRenderer $fallback,
    ) {
    }

    /**
     * Register a renderer for a specific tool.
     */
    public function register(string $toolName, ToolRenderer $renderer): void
    {
        $this->map[$toolName] = $renderer;
    }

    /**
     * Render a tool call using the registered renderer.
     */
    public function render(string $toolName, string $arguments): string
    {
        return ($this->map[$toolName] ?? $this->fallback)->render($toolName, $arguments);
    }

    /**
     * Get the fallback renderer.
     */
    public function fallback(): ToolRenderer
    {
        return $this->fallback;
    }

    /**
     * Check if a renderer is registered for a tool.
     */
    public function has(string $toolName): bool
    {
        return isset($this->map[$toolName]);
    }

    /**
     * Get all registered tool names.
     *
     * @return string[]
     */
    public function registeredTools(): array
    {
        return array_keys($this->map);
    }
}
