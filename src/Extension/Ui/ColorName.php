<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Ui;

/**
 * Standard semantic color names.
 */
enum ColorName: string
{
    case PRIMARY = 'primary';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'error';
    case INFO = 'info';
    case MUTED = 'muted';
    case ACCENT = 'accent';
}
