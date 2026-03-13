<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Registry;

use InvalidArgumentException;
use NeuronCore\Maestro\Console\Inline\InlineCommand;
use NeuronCore\Maestro\Extension\Registry\CommandRegistry;
use PHPUnit\Framework\TestCase;

class CommandRegistryTest extends TestCase
{
    public function testRegisterStoresCommand(): void
    {
        $command = $this->createCommandMock('test', 'Test command');
        $registry = new CommandRegistry();

        $registry->register($command);

        $this->assertSame($command, $registry->get('test'));
    }

    public function testRegisterThrowsOnDuplicate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Inline command "test" is already registered.');

        $registry = new CommandRegistry();
        $registry->register($this->createCommandMock('test', 'First'));
        $registry->register($this->createCommandMock('test', 'Second'));
    }

    public function testGetReturnsNullForUnknownCommand(): void
    {
        $registry = new CommandRegistry();

        $this->assertNull($registry->get('unknown'));
    }

    public function testHasReturnsCorrectly(): void
    {
        $registry = new CommandRegistry();
        $registry->register($this->createCommandMock('existing', 'Exists'));

        $this->assertTrue($registry->has('existing'));
        $this->assertFalse($registry->has('unknown'));
    }

    public function testAllReturnsAllCommands(): void
    {
        $command1 = $this->createCommandMock('cmd1', 'Command 1');
        $command2 = $this->createCommandMock('cmd2', 'Command 2');
        $registry = new CommandRegistry();

        $registry->register($command1);
        $registry->register($command2);

        $all = $registry->all();

        $this->assertCount(2, $all);
        $this->assertSame($command1, $all['cmd1']);
        $this->assertSame($command2, $all['cmd2']);
    }

    public function testListCommandsReturnsSorted(): void
    {
        $registry = new CommandRegistry();
        $registry->register($this->createCommandMock('zebra', 'Last'));
        $registry->register($this->createCommandMock('alpha', 'First'));
        $registry->register($this->createCommandMock('middle', 'Middle'));

        $list = $registry->listCommands();

        $this->assertSame('alpha', $list[0]['name']);
        $this->assertSame('middle', $list[1]['name']);
        $this->assertSame('zebra', $list[2]['name']);
    }

    public function testListCommandsReturnsNameAndDescription(): void
    {
        $registry = new CommandRegistry();
        $registry->register($this->createCommandMock('test', 'Test description'));

        $list = $registry->listCommands();

        $this->assertCount(1, $list);
        $this->assertSame('test', $list[0]['name']);
        $this->assertSame('Test description', $list[0]['description']);
    }

    public function testListCommandsReturnsEmptyForEmptyRegistry(): void
    {
        $registry = new CommandRegistry();

        $list = $registry->listCommands();

        $this->assertSame([], $list);
    }

    private function createCommandMock(string $name, string $description): InlineCommand
    {
        $mock = $this->createMock(InlineCommand::class);
        $mock->method('getName')->willReturn($name);
        $mock->method('getDescription')->willReturn($description);
        return $mock;
    }
}
