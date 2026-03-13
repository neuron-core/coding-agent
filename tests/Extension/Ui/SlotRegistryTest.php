<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Ui;

use NeuronCore\Maestro\Extension\Ui\SlotRegistry;
use NeuronCore\Maestro\Extension\Ui\SlotType;
use PHPUnit\Framework\TestCase;

class SlotRegistryTest extends TestCase
{
    public function testSlotReturnsSameInstance(): void
    {
        $registry = new SlotRegistry();
        $slot1 = $registry->slot(SlotType::HEADER);
        $slot2 = $registry->slot(SlotType::HEADER);

        $this->assertSame($slot1, $slot2);
    }

    public function testClearSlot(): void
    {
        $registry = new SlotRegistry();
        $slot = $registry->slot(SlotType::CONTENT);
        $slot->add('Content');

        $this->assertFalse($slot->isEmpty());

        $registry->clear(SlotType::CONTENT);

        $this->assertTrue($slot->isEmpty());
    }

    public function testClearAll(): void
    {
        $registry = new SlotRegistry();
        $registry->slot(SlotType::HEADER)->add('Header item');
        $registry->slot(SlotType::FOOTER)->add('Footer item');

        $this->assertFalse($registry->slot(SlotType::HEADER)->isEmpty());
        $this->assertFalse($registry->slot(SlotType::FOOTER)->isEmpty());

        $registry->clearAll();

        $this->assertTrue($registry->slot(SlotType::HEADER)->isEmpty());
        $this->assertTrue($registry->slot(SlotType::FOOTER)->isEmpty());
    }

    public function testNamesReturnsAllSlotNames(): void
    {
        $registry = new SlotRegistry();
        $registry->slot(SlotType::HEADER);
        $registry->slot(SlotType::CONTENT);
        $registry->slot(SlotType::FOOTER);

        $this->assertSame(
            [SlotType::HEADER->value, SlotType::CONTENT->value, SlotType::FOOTER->value],
            $registry->names(),
        );
    }

    public function testAllReturnsAllSlots(): void
    {
        $registry = new SlotRegistry();
        $header = $registry->slot(SlotType::HEADER);
        $footer = $registry->slot(SlotType::FOOTER);

        $all = $registry->all();

        $this->assertCount(2, $all);
        $this->assertSame($header, $all[SlotType::HEADER->value]);
        $this->assertSame($footer, $all[SlotType::FOOTER->value]);
    }
}
