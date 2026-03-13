<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Registry;

use NeuronAI\Tools\ToolInterface;
use InvalidArgumentException;

use function array_values;
use function sprintf;

/**
 * Registry for AI tools that extensions can register.
 */
class ToolRegistry
{
    /** @var array<string, ToolInterface> */
    protected array $tools = [];

    /**
     * Register a tool.
     *
     * @throws InvalidArgumentException if a tool with the same name is already registered
     */
    public function register(ToolInterface $tool): void
    {
        $name = $tool->getName();

        if (isset($this->tools[$name])) {
            throw new InvalidArgumentException(
                sprintf('Tool "%s" is already registered.', $name)
            );
        }

        $this->tools[$name] = $tool;
    }

    /**
     * Get a registered tool by name.
     */
    public function get(string $name): ?ToolInterface
    {
        return $this->tools[$name] ?? null;
    }

    /**
     * Check if a tool is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->tools[$name]);
    }

    /**
     * Get all registered tools.
     *
     * @return array<string, ToolInterface>
     */
    public function all(): array
    {
        return $this->tools;
    }

    /**
     * Get all tools as a list (for NeuronAI compatibility).
     *
     * @return ToolInterface[]
     */
    public function list(): array
    {
        return array_values($this->tools);
    }
}
