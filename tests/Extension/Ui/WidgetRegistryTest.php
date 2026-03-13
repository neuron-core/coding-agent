<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Ui;

use NeuronCore\Maestro\Extension\Ui\ContentType;
use NeuronCore\Maestro\Extension\Ui\WidgetInterface;
use NeuronCore\Maestro\Extension\Ui\WidgetRegistry;
use PHPUnit\Framework\TestCase;

class WidgetRegistryTest extends TestCase
{
    private function createWidget(string $name, string $contentType): WidgetInterface
    {
        $mock = $this->createMock(WidgetInterface::class);
        $mock->method('name')->willReturn($name);
        $mock->method('contentType')->willReturn($contentType);
        return $mock;
    }

    public function testRegisterStoresWidget(): void
    {
        $registry = new WidgetRegistry();
        $widget = $this->createWidget('test', 'tool_call');

        $registry->register($widget);

        $this->assertTrue($registry->has('test'));
        $this->assertSame($widget, $registry->get('test'));
    }

    public function testGetReturnsNullForUnknownWidget(): void
    {
        $registry = new WidgetRegistry();

        $this->assertNull($registry->get('unknown'));
    }

    public function testHasReturnsCorrectly(): void
    {
        $registry = new WidgetRegistry();
        $registry->register($this->createWidget('existing', 'tool_call'));

        $this->assertTrue($registry->has('existing'));
        $this->assertFalse($registry->has('unknown'));
    }

    public function testForTypeReturnsMatchingWidgets(): void
    {
        $registry = new WidgetRegistry();
        $registry->register($this->createWidget('tool1', 'tool_call'));
        $registry->register($this->createWidget('tool2', 'tool_call'));
        $registry->register($this->createWidget('response', 'agent_response'));
        $registry->register($this->createWidget('thinking', 'agent_thinking'));

        $toolWidgets = $registry->forType('tool_call');

        $this->assertCount(2, $toolWidgets);
        $this->assertContainsOnlyInstancesOf(WidgetInterface::class, $toolWidgets);
    }

    public function testNamesReturnsAllWidgetNames(): void
    {
        $registry = new WidgetRegistry();
        $registry->register($this->createWidget('widget1', ContentType::TOOL_CALL->value));
        $registry->register($this->createWidget('widget2', ContentType::TOOL_CALL->value));

        $this->assertSame(['widget1', 'widget2'], $registry->names());
    }

    public function testAllReturnsAllWidgets(): void
    {
        $registry = new WidgetRegistry();
        $w1 = $this->createWidget('w1', ContentType::TOOL_CALL->value);
        $w2 = $this->createWidget('w2', ContentType::TOOL_CALL->value);

        $registry->register($w1);
        $registry->register($w2);

        $all = $registry->all();

        $this->assertCount(2, $all);
        $this->assertSame($w1, $all['w1']);
        $this->assertSame($w2, $all['w2']);
    }
}
