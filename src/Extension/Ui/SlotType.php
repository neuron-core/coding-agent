<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Ui;

/**
 * Predefined UI slot types.
 */
enum SlotType: string
{
    case HEADER = 'header';
    case STATUS_BAR = 'status_bar';
    case CONTENT = 'content';
    case FOOTER = 'footer';
}
