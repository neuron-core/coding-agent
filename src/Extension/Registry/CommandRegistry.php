<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Registry;

use InvalidArgumentException;
use NeuronCore\Maestro\Console\Inline\InlineCommand;

use function sprintf;
use function usort;

/**
 * Registry for inline commands that extensions can register.
 */
final class CommandRegistry
{
    /** @var array<string, InlineCommand> */
    private array $commands = [];

    /**
     * Register an inline command.
     *
     * @throws InvalidArgumentException if a command with the same name is already registered
     */
    public function register(InlineCommand $command): void
    {
        $name = $command->getName();

        if (isset($this->commands[$name])) {
            throw new InvalidArgumentException(
                sprintf('Inline command "%s" is already registered.', $name)
            );
        }

        $this->commands[$name] = $command;
    }

    /**
     * Get a registered command by name.
     */
    public function get(string $name): ?InlineCommand
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * Check if a command is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    /**
     * Get all registered commands indexed by name.
     *
     * @return array<string, InlineCommand>
     */
    public function all(): array
    {
        return $this->commands;
    }

    /**
     * Get a list of command names and descriptions for display.
     *
     * @return array<array{name: string, description: string}>
     */
    public function listCommands(): array
    {
        $list = [];
        foreach ($this->commands as $name => $command) {
            $list[] = [
                'name' => $name,
                'description' => $command->getDescription(),
            ];
        }

        usort($list, fn (array $a, array $b): int => $a['name'] <=> $b['name']);

        return $list;
    }
}
