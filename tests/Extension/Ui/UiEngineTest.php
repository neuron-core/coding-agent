<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Ui;

use NeuronCore\Maestro\Extension\Ui\SlotRegistry;
use NeuronCore\Maestro\Extension\Ui\SlotType;
use NeuronCore\Maestro\Extension\Ui\ThemeInterface;
use NeuronCore\Maestro\Extension\Ui\UiBuilder;
use NeuronCore\Maestro\Extension\Ui\UiEngine;
use NeuronCore\Maestro\Extension\Ui\WidgetRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class UiEngineTest extends TestCase
{
    protected function createEngine(?ThemeInterface $theme = null): UiEngine
    {
        $theme ??= $this->createMock(ThemeInterface::class);
        return new UiEngine($theme, new SlotRegistry(), new WidgetRegistry());
    }

    public function testSetThemeChangesActiveTheme(): void
    {
        $engine = $this->createEngine();
        $newTheme = $this->createMock(ThemeInterface::class);
        $newTheme->method('name')->willReturn('custom');

        $engine->setTheme($newTheme);

        $this->assertSame($newTheme, $engine->theme());
    }

    public function testCreateBuilderReturnsBoundBuilder(): void
    {
        $engine = $this->createEngine();
        $builder = $engine->createBuilder();

        $this->assertInstanceOf(UiBuilder::class, $builder);
        $this->assertSame($engine->theme(), $builder->theme());
    }

    public function testThemeChangePropagatesViaBuilder(): void
    {
        $engine = $this->createEngine();
        $builder = $engine->createBuilder();

        $newTheme = $this->createMock(ThemeInterface::class);
        $newTheme->method('name')->willReturn('new');

        $builder->registerTheme($newTheme);

        $this->assertSame($newTheme, $engine->theme());
        $this->assertSame($newTheme, $builder->theme());
    }

    public function testDefaultSlotsAreInitialized(): void
    {
        $engine = $this->createEngine();
        $names = $engine->slots()->names();

        $this->assertContains(SlotType::HEADER->value, $names);
        $this->assertContains(SlotType::CONTENT->value, $names);
    }

    public function testRenderHeaderOutputsSlotContent(): void
    {
        $engine = $this->createEngine();
        $engine->slots()->slot(SlotType::HEADER)->add('My Header');

        $output = new BufferedOutput();
        $engine->renderHeader($output);

        $this->assertStringContainsString('My Header', $output->fetch());
    }

    public function testClearSlotsEmptiesAllSlots(): void
    {
        $engine = $this->createEngine();
        $engine->slots()->slot(SlotType::HEADER)->add('Something');

        $engine->clearSlots();

        $this->assertTrue($engine->slots()->slot(SlotType::HEADER)->isEmpty());
    }
}
