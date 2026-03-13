<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Registry;

use NeuronCore\Maestro\Extension\Registry\RendererRegistry;
use NeuronCore\Maestro\Rendering\ToolRenderer;
use PHPUnit\Framework\TestCase;

class RendererRegistryTest extends TestCase
{
    public function testRegisterStoresRenderer(): void
    {
        $fallback = $this->createMockRenderer();
        $registry = new RendererRegistry($fallback);
        $renderer = $this->createMockRenderer();

        $registry->register('my_tool', $renderer);

        $this->assertTrue($registry->has('my_tool'));
    }

    public function testRenderUsesRegisteredRenderer(): void
    {
        $fallback = $this->createMockRenderer();
        $fallback->expects($this->never())->method('render');

        $registered = $this->createMockRenderer();
        $registered->expects($this->once())
            ->method('render')
            ->with('my_tool', '{}')
            ->willReturn('registered output');

        $registry = new RendererRegistry($fallback);
        $registry->register('my_tool', $registered);

        $this->assertSame('registered output', $registry->render('my_tool', '{}'));
    }

    public function testRenderUsesFallbackForUnknownTool(): void
    {
        $fallback = $this->createMockRenderer();
        $fallback->expects($this->once())
            ->method('render')
            ->with('unknown_tool', '{}')
            ->willReturn('fallback output');

        $registry = new RendererRegistry($fallback);

        $this->assertSame('fallback output', $registry->render('unknown_tool', '{}'));
    }

    public function testHasReturnsCorrectly(): void
    {
        $fallback = $this->createMockRenderer();
        $registry = new RendererRegistry($fallback);
        $registry->register('existing', $this->createMockRenderer());

        $this->assertTrue($registry->has('existing'));
        $this->assertFalse($registry->has('unknown'));
    }

    public function testFallbackReturnsTheSameInstance(): void
    {
        $fallback = $this->createMockRenderer();
        $registry = new RendererRegistry($fallback);

        $this->assertSame($fallback, $registry->fallback());
    }

    public function testRegisteredToolsReturnsToolNames(): void
    {
        $fallback = $this->createMockRenderer();
        $registry = new RendererRegistry($fallback);
        $registry->register('tool1', $this->createMockRenderer());
        $registry->register('tool2', $this->createMockRenderer());

        $tools = $registry->registeredTools();

        $this->assertCount(2, $tools);
        $this->assertContains('tool1', $tools);
        $this->assertContains('tool2', $tools);
    }

    public function testRegisteredToolsReturnsEmptyForNoRegistrations(): void
    {
        $registry = new RendererRegistry($this->createMockRenderer());

        $this->assertSame([], $registry->registeredTools());
    }

    private function createMockRenderer(): mixed
    {
        return $this->createMock(ToolRenderer::class);
    }
}
