<?php

declare(strict_types=1);

namespace NeuronCore\Synapse\Commands;

use function str_repeat;

trait CommandHelper
{
    protected function clearOutput(): void
    {
        $this->output->write("\r" . str_repeat(' ', 50) . "\r");
    }
}
