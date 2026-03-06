<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Events;

class AgentResponseEvent
{
    public function __construct(public readonly string $content)
    {
    }
}
