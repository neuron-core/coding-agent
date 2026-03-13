<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Ui;

use NeuronCore\Maestro\Extension\Ui\SlotContent;
use PHPUnit\Framework\TestCase;

class SlotContentTest extends TestCase
{
    public function testAddItem(): void
    {
        $content = new SlotContent('test_slot');

        $content->add('First item', 100);
        $content->add('Second item', 200);
        $content->add('Third item', 50);

        $this->assertFalse($content->isEmpty());
        $this->assertSame(['Second item', 'First item', 'Third item'], $content->sorted());
    }

    public function testClear(): void
    {
        $content = new SlotContent('test_slot');
        $content->add('Item');

        $this->assertFalse($content->isEmpty());

        $content->clear();

        $this->assertTrue($content->isEmpty());
        $this->assertSame([], $content->sorted());
    }

    public function testIsEmpty(): void
    {
        $content = new SlotContent('test_slot');

        $this->assertTrue($content->isEmpty());
    }

    public function testSlotName(): void
    {
        $content = new SlotContent('my_slot');

        $this->assertSame('my_slot', $content->slotName());
    }

    public function testPriorityOrdering(): void
    {
        $content = new SlotContent('test_slot');
        $content->add('Low priority', 100);
        $content->add('Medium priority', 500);
        $content->add('High priority', 900);

        $sorted = $content->sorted();

        $this->assertSame('High priority', $sorted[0]);
        $this->assertSame('Medium priority', $sorted[1]);
        $this->assertSame('Low priority', $sorted[2]);
    }
}
