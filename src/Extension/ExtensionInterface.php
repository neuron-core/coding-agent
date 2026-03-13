<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension;

/**
 * Interface that all Maestro extensions must implement.
 */
interface ExtensionInterface
{
    /**
     * Get the extension name.
     */
    public function name(): string;

    /**
     * Register the extension with the Maestro application.
     */
    public function register(ExtensionApi $api): void;
}
