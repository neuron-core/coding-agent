<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Ui;

/**
 * Defines colors and styles for CLI output.
 */
interface ThemeInterface
{
    /**
     * Get theme name.
     */
    public function name(): string;

    /**
     * Get the terminal color code for a semantic color.
     */
    public function color(ColorName $color): string;

    /**
     * Get the Symfony Console style string for a semantic style.
     *
     * @return string e.g., "options=bold"
     */
    public function style(StyleName $style): string;

    /**
     * Get the glyph for a semantic icon.
     */
    public function icon(IconName $icon): string;
}
