<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension;

use NeuronCore\Maestro\Extension\ExtensionDescriptor;
use PHPUnit\Framework\TestCase;

class ExtensionDescriptorTest extends TestCase
{
    public function testCreateReturnsDescriptor(): void
    {
        $descriptor = new ExtensionDescriptor(
            className: 'TestExtension',
            name: 'test',
            enabled: true,
            config: ['key' => 'value'],
        );

        $this->assertSame('TestExtension', $descriptor->className);
        $this->assertSame('test', $descriptor->name);
        $this->assertTrue($descriptor->enabled);
        $this->assertSame(['key' => 'value'], $descriptor->config);
    }

    public function testDisabledCreatesDisabledDescriptor(): void
    {
        $descriptor = ExtensionDescriptor::disabled('TestExtension', 'test');

        $this->assertSame('TestExtension', $descriptor->className);
        $this->assertSame('test', $descriptor->name);
        $this->assertFalse($descriptor->enabled);
        $this->assertSame([], $descriptor->config);
    }

    public function testCreateWithDefaults(): void
    {
        $descriptor = new ExtensionDescriptor(
            className: 'TestExtension',
            name: 'test',
        );

        $this->assertTrue($descriptor->enabled);
        $this->assertSame([], $descriptor->config);
    }
}
