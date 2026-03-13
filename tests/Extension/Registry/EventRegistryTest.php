<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Extension\Registry;

use NeuronCore\Maestro\Extension\Registry\EventRegistry;
use PHPUnit\Framework\TestCase;

class EventRegistryTest extends TestCase
{
    public function testRegisterStoresHandler(): void
    {
        $registry = new EventRegistry();
        $handler = fn (): string => 'test';

        $registry->register('test.event', $handler);

        $this->assertTrue($registry->has('test.event'));
    }

    public function testHandlersForReturnsAllHandlers(): void
    {
        $registry = new EventRegistry();
        $handler1 = fn (): string => 'first';
        $handler2 = fn (): string => 'second';

        $registry->register('test.event', $handler1);
        $registry->register('test.event', $handler2);

        $handlers = $registry->handlersFor('test.event');

        $this->assertCount(2, $handlers);
        $this->assertSame($handler1, $handlers[0]);
        $this->assertSame($handler2, $handlers[1]);
    }

    public function testHandlersForReturnsEmptyForUnknownEvent(): void
    {
        $registry = new EventRegistry();

        $this->assertSame([], $registry->handlersFor('unknown.event'));
    }

    public function testHasReturnsCorrectly(): void
    {
        $registry = new EventRegistry();
        $registry->register('existing.event', fn () => null);

        $this->assertTrue($registry->has('existing.event'));
        $this->assertFalse($registry->has('unknown.event'));
    }

    public function testRegisteredEventsReturnsAllEventNames(): void
    {
        $registry = new EventRegistry();
        $registry->register('event1', fn () => null);
        $registry->register('event2', fn () => null);
        $registry->register('event3', fn () => null);

        $events = $registry->registeredEvents();

        $this->assertCount(3, $events);
        $this->assertContains('event1', $events);
        $this->assertContains('event2', $events);
        $this->assertContains('event3', $events);
    }

    public function testClearRemovesAllHandlers(): void
    {
        $registry = new EventRegistry();
        $registry->register('test.event', fn () => null);
        $registry->register('test.event', fn () => null);

        $this->assertTrue($registry->has('test.event'));

        $registry->clear('test.event');

        $this->assertFalse($registry->has('test.event'));
    }

    public function testClearDoesNotAffectOtherEvents(): void
    {
        $registry = new EventRegistry();
        $registry->register('event1', fn () => null);
        $registry->register('event2', fn () => null);

        $registry->clear('event1');

        $this->assertFalse($registry->has('event1'));
        $this->assertTrue($registry->has('event2'));
    }
}
