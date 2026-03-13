<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Registry;

use NeuronAI\Tools\ToolInterface;
use NeuronCore\Maestro\Extension\Registry\ToolRegistry;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class ToolRegistryTest extends TestCase
{
    public function testRegisterStoresTool(): void
    {
        $tool = $this->createToolMock('test_tool');
        $registry = new ToolRegistry();

        $registry->register($tool);

        $this->assertSame($tool, $registry->get('test_tool'));
    }

    public function testRegisterThrowsOnDuplicate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tool "test_tool" is already registered.');

        $registry = new ToolRegistry();
        $registry->register($this->createToolMock('test_tool'));
        $registry->register($this->createToolMock('test_tool'));
    }

    public function testGetReturnsNullForUnknownTool(): void
    {
        $registry = new ToolRegistry();

        $this->assertNull($registry->get('unknown'));
    }

    public function testHasReturnsCorrectly(): void
    {
        $registry = new ToolRegistry();
        $registry->register($this->createToolMock('existing'));

        $this->assertTrue($registry->has('existing'));
        $this->assertFalse($registry->has('unknown'));
    }

    public function testAllReturnsAllTools(): void
    {
        $tool1 = $this->createToolMock('tool1');
        $tool2 = $this->createToolMock('tool2');
        $registry = new ToolRegistry();

        $registry->register($tool1);
        $registry->register($tool2);

        $all = $registry->all();

        $this->assertCount(2, $all);
        $this->assertSame($tool1, $all['tool1']);
        $this->assertSame($tool2, $all['tool2']);
    }

    public function testListReturnsValuesOnly(): void
    {
        $tool1 = $this->createToolMock('tool1');
        $tool2 = $this->createToolMock('tool2');
        $registry = new ToolRegistry();

        $registry->register($tool1);
        $registry->register($tool2);

        $list = $registry->list();

        $this->assertCount(2, $list);
        $this->assertContainsOnlyInstancesOf(ToolInterface::class, $list);
    }

    private function createToolMock(string $name): ToolInterface
    {
        $mock = $this->createMock(ToolInterface::class);
        $mock->method('getName')->willReturn($name);
        return $mock;
    }
}
