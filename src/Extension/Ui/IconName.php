<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Ui;

/**
 * Standard icon names.
 */
enum IconName: string
{
    case SPINNER = 'spinner';
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';
    case ARROW_RIGHT = 'arrow_right';
    case ARROW_DOWN = 'arrow_down';
    case DOT = 'dot';
}
