<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Extension\Ui;

/**
 * Standard content types for widgets.
 */
enum ContentType: string
{
    case TOOL_CALL = 'tool_call';
    case AGENT_RESPONSE = 'agent_response';
    case AGENT_THINKING = 'agent_thinking';
    case STATUS = 'status';
}
