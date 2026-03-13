<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension;

use NeuronCore\Maestro\Extension\Registry\CommandRegistry;
use NeuronCore\Maestro\Extension\Registry\EventRegistry;
use NeuronCore\Maestro\Extension\Registry\RendererRegistry;
use NeuronCore\Maestro\Extension\Registry\ToolRegistry;
use NeuronCore\Maestro\Rendering\ToolRenderer;
use Throwable;
use InvalidArgumentException;
use RuntimeException;

use function class_exists;
use function sprintf;

/**
 * Loads and initializes extensions from configuration.
 */
final class ExtensionLoader
{
    /** @var array<ExtensionDescriptor> */
    private array $descriptors = [];

    public function __construct(
        private readonly ToolRegistry $tools,
        private readonly CommandRegistry $commands,
        private readonly RendererRegistry $renderers,
        private readonly EventRegistry $events,
    ) {
    }

    /**
     * Load extensions from the settings array.
     *
     * @param array{extensions?: array<int, array{class: string, enabled?: bool, config?: array<string, mixed>}>} $settings
     * @return array<ExtensionDescriptor>
     */
    public function load(array $settings): array
    {
        $extensions = $settings['extensions'] ?? [];

        foreach ($extensions as $config) {
            $className = $config['class'] ?? null;
            $enabled = $config['enabled'] ?? true;
            $config = $config['config'] ?? [];
            if ($className === null) {
                continue;
            }
            if (!class_exists($className)) {
                continue;
            }

            $descriptor = new ExtensionDescriptor(
                className: $className,
                name: $className,
                enabled: $enabled,
                config: $config,
            );

            if ($enabled) {
                $this->initialize($descriptor);
            }

            $this->descriptors[] = $descriptor;
        }

        return $this->descriptors;
    }

    /**
     * Initialize an extension by instantiating it and calling register().
     *
     * @throws InvalidArgumentException if the class doesn't implement ExtensionInterface
     * @throws RuntimeException if the extension fails to initialize
     */
    private function initialize(ExtensionDescriptor $descriptor): void
    {
        try {
            $instance = new $descriptor->className();

            if (!$instance instanceof ExtensionInterface) {
                throw new InvalidArgumentException(
                    sprintf('Extension class "%s" must implement %s.', $descriptor->className, ExtensionInterface::class)
                );
            }

            $api = new ExtensionApi(
                tools: $this->tools,
                commands: $this->commands,
                renderers: $this->renderers,
                events: $this->events,
            );

            $instance->register($api);
        } catch (Throwable $e) {
            throw new RuntimeException(sprintf('Failed to initialize extension "%s": %s', $descriptor->className, $e->getMessage()), $e->getCode(), previous: $e);
        }
    }

    /**
     * Get all loaded extension descriptors.
     *
     * @return array<ExtensionDescriptor>
     */
    public function descriptors(): array
    {
        return $this->descriptors;
    }

    /**
     * Get the tool registry.
     */
    public function tools(): ToolRegistry
    {
        return $this->tools;
    }

    /**
     * Get the command registry.
     */
    public function commands(): CommandRegistry
    {
        return $this->commands;
    }

    /**
     * Get the renderer registry.
     */
    public function renderers(): RendererRegistry
    {
        return $this->renderers;
    }

    /**
     * Get the event registry.
     */
    public function events(): EventRegistry
    {
        return $this->events;
    }

    /**
     * Create a loader with default registries.
     */
    public static function create(ToolRenderer $fallbackRenderer): self
    {
        return new self(
            tools: new ToolRegistry(),
            commands: new CommandRegistry(),
            renderers: new RendererRegistry($fallbackRenderer),
            events: new EventRegistry(),
        );
    }
}
