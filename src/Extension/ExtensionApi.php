<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension;

use NeuronCore\Maestro\Console\Inline\InlineCommand;
use NeuronCore\Maestro\Extension\Registry\CommandRegistry;
use NeuronCore\Maestro\Extension\Registry\EventRegistry;
use NeuronCore\Maestro\Extension\Registry\RendererRegistry;
use NeuronCore\Maestro\Extension\Registry\ToolRegistry;
use NeuronCore\Maestro\Extension\Ui\UiBuilder;
use NeuronCore\Maestro\Extension\Ui\WidgetInterface;
use NeuronCore\Maestro\Rendering\ToolRenderer;
use NeuronAI\Tools\ToolInterface;

/**
 * API passed to extensions for registering components.
 */
class ExtensionApi
{
    public function __construct(
        protected readonly ToolRegistry $tools,
        protected readonly CommandRegistry $commands,
        protected readonly RendererRegistry $renderers,
        protected readonly EventRegistry $events,
        protected readonly UiBuilder $ui,
    ) {
    }

    /**
     * Register a tool that the AI agent can use.
     */
    public function registerTool(ToolInterface $tool): void
    {
        $this->tools->register($tool);
    }

    /**
     * Register an inline command available in the interactive console.
     */
    public function registerCommand(InlineCommand $command): void
    {
        $this->commands->register($command);
    }

    /**
     * Register a custom renderer for a specific tool.
     */
    public function registerRenderer(string $toolName, ToolRenderer $renderer): void
    {
        $this->renderers->register($toolName, $renderer);
    }

    /**
     * Register a callback for a specific event.
     */
    public function on(string $event, callable $handler): void
    {
        $this->events->register($event, $handler);
    }

    /**
     * Register a UI widget for rendering specific content types.
     */
    public function registerWidget(WidgetInterface $widget): void
    {
        $this->ui->registerWidget($widget);
    }

    /**
     * Get the tool registry for advanced registration needs.
     */
    public function tools(): ToolRegistry
    {
        return $this->tools;
    }

    /**
     * Get the command registry for advanced registration needs.
     */
    public function commands(): CommandRegistry
    {
        return $this->commands;
    }

    /**
     * Get the renderer registry for advanced registration needs.
     */
    public function renderers(): RendererRegistry
    {
        return $this->renderers;
    }

    /**
     * Get the event registry for advanced registration needs.
     */
    public function events(): EventRegistry
    {
        return $this->events;
    }

    /**
     * Get the UI builder for UI customization.
     */
    public function ui(): UiBuilder
    {
        return $this->ui;
    }
}
