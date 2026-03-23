<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Ui\Theme;

use NeuronCore\Maestro\Extension\Ui\ColorName;
use NeuronCore\Maestro\Extension\Ui\IconName;
use NeuronCore\Maestro\Extension\Ui\StyleName;
use NeuronCore\Maestro\Extension\Ui\Theme\DarkTheme;
use PHPUnit\Framework\TestCase;

class DarkThemeTest extends TestCase
{
    public function testName(): void
    {
        $theme = new DarkTheme();

        $this->assertSame('dark', $theme->name());
    }

    public function testColorPrimary(): void
    {
        $this->assertSame('cyan', (new DarkTheme())->color(ColorName::PRIMARY));
    }

    public function testColorSuccess(): void
    {
        $this->assertSame('green', (new DarkTheme())->color(ColorName::SUCCESS));
    }

    public function testColorWarning(): void
    {
        $this->assertSame('yellow', (new DarkTheme())->color(ColorName::WARNING));
    }

    public function testColorError(): void
    {
        $this->assertSame('red', (new DarkTheme())->color(ColorName::ERROR));
    }

    public function testColorInfo(): void
    {
        $this->assertSame('blue', (new DarkTheme())->color(ColorName::INFO));
    }

    public function testColorMuted(): void
    {
        $this->assertSame('gray', (new DarkTheme())->color(ColorName::MUTED));
    }

    public function testColorAccent(): void
    {
        $this->assertSame('magenta', (new DarkTheme())->color(ColorName::ACCENT));
    }

    public function testStyleBold(): void
    {
        $this->assertSame('options=bold', (new DarkTheme())->style(StyleName::BOLD));
    }

    public function testStyleUnderline(): void
    {
        $this->assertSame('options=underscore', (new DarkTheme())->style(StyleName::UNDERLINE));
    }

    public function testStyleDefault(): void
    {
        $this->assertSame('', (new DarkTheme())->style(StyleName::DEFAULT));
    }

    public function testIconSpinner(): void
    {
        $this->assertSame('⠋', (new DarkTheme())->icon(IconName::SPINNER));
    }

    public function testIconWarning(): void
    {
        $this->assertSame('⚠', (new DarkTheme())->icon(IconName::WARNING));
    }

    public function testIconDot(): void
    {
        $this->assertSame('·', (new DarkTheme())->icon(IconName::DOT));
    }
}
