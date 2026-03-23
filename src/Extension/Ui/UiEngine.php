<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Ui;

use Symfony\Component\Console\Output\OutputInterface;

use function str_repeat;

/**
 * Main UI engine for rendering CLI output with themes and slots.
 */
class UiEngine
{
    public function __construct(
        protected ThemeInterface $theme,
        protected readonly SlotRegistry $slots,
        protected readonly WidgetRegistry $widgets,
    ) {
        Text::setTheme($this->theme);

        // Initialize default slots
        $this->slots->slot(SlotType::HEADER);
        $this->slots->slot(SlotType::CONTENT);
    }

    /**
     * Render a complete output with all active slots.
     */
    public function render(OutputInterface $output): void
    {
        $this->renderHeader($output);
        $this->renderContent($output);
    }

    /**
     * Render header slot.
     */
    public function renderHeader(OutputInterface $output): void
    {
        $content = $this->slots->slot(SlotType::HEADER);
        if ($content->isEmpty()) {
            return;
        }

        foreach ($content->sorted() as $item) {
            $output->writeln($item);
        }
        $output->writeln('');
    }

    /**
     * Render main content slot.
     */
    public function renderContent(OutputInterface $output): void
    {
        $content = $this->slots->slot(SlotType::CONTENT);
        foreach ($content->sorted() as $line) {
            $output->writeln($line);
        }
    }

    /**
     * Render a horizontal line separator.
     */
    public function renderSeparator(OutputInterface $output): void
    {
        $output->writeln(Text::content(str_repeat('─', 60))->muted()->build());
    }

    /**
     * Get the current theme.
     */
    public function theme(): ThemeInterface
    {
        return $this->theme;
    }

    /**
     * Get the slot registry.
     */
    public function slots(): SlotRegistry
    {
        return $this->slots;
    }

    /**
     * Get the widget registry.
     */
    public function widgets(): WidgetRegistry
    {
        return $this->widgets;
    }

    /**
     * Create a UiBuilder for extensions, bound to this engine.
     */
    public function createBuilder(): UiBuilder
    {
        $builder = new UiBuilder(
            $this->theme,
            $this->slots,
            $this->widgets,
        );
        $builder->setEngine($this);
        return $builder;
    }

    /**
     * Set the current theme.
     */
    public function setTheme(ThemeInterface $theme): void
    {
        $this->theme = $theme;
        Text::setTheme($theme);
    }

    /**
     * Clear all slots.
     */
    public function clearSlots(): void
    {
        $this->slots->clearAll();
    }
}
