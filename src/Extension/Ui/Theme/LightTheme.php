<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Ui\Theme;

use NeuronCore\Maestro\Extension\Ui\ColorName;
use NeuronCore\Maestro\Extension\Ui\IconName;
use NeuronCore\Maestro\Extension\Ui\StyleName;
use NeuronCore\Maestro\Extension\Ui\ThemeInterface;

/**
 * Light color theme for CLI output.
 */
class LightTheme implements ThemeInterface
{
    public function name(): string
    {
        return 'light';
    }

    public function color(ColorName $color): string
    {
        return match ($color) {
            ColorName::PRIMARY => 'blue',
            ColorName::SUCCESS => 'green',
            ColorName::WARNING => 'yellow',
            ColorName::ERROR   => 'red',
            ColorName::INFO    => 'cyan',
            ColorName::MUTED   => 'gray',
            ColorName::ACCENT  => 'blue',
        };
    }

    public function style(StyleName $style): string
    {
        return match ($style) {
            StyleName::BOLD      => 'options=bold',
            StyleName::DIM       => 'options=dim',
            StyleName::UNDERLINE => 'options=underscore',
            StyleName::DEFAULT   => '',
        };
    }

    public function icon(IconName $icon): string
    {
        return match ($icon) {
            IconName::SPINNER     => '⠋',
            IconName::SUCCESS     => '',
            IconName::ERROR       => '',
            IconName::WARNING     => '⚠',
            IconName::INFO        => '',
            IconName::ARROW_RIGHT => '',
            IconName::ARROW_DOWN  => '',
            IconName::DOT         => '·',
        };
    }
}
