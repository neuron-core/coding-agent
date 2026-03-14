---
name: maestro-extension
description: Help developers create and implement extensions for the Maestro CLI application. Use this skill whenever the user mentions creating a Maestro extension, adding custom tools or commands, building UI components for Maestro, registering memory files or event handlers, packaging a Composer package with Maestro extensions, or setting up auto-discovery in composer.json. Also trigger when users ask about the ExtensionInterface, ExtensionApi, InlineCommand, ToolInterface, ThemeInterface, WidgetInterface, or any Maestro extension-related classes and concepts.
---

# Maestro Extension Development Guide

This skill helps you create and implement extensions for the Maestro CLI application. Maestro extensions allow you to customize the AI agent by adding custom tools, commands, renderers, UI components, and event handlers.

## Core Concepts

A Maestro extension is a PHP class that implements `ExtensionInterface`. The extension registers its capabilities through the `ExtensionApi` object in the `register()` method.

### Extension Structure

```php
namespace MyVendor\MyExtension;

use NeuronCore\Maestro\Extension\ExtensionInterface;
use NeuronCore\Maestro\Extension\ExtensionApi;

class MyExtension implements ExtensionInterface
{
    public function name(): string
    {
        return 'my-extension';
    }

    public function register(ExtensionApi $api): void
    {
        // Register tools, commands, renderers, events, etc.
    }
}
```

---

## Reading Documentation

When working on an extension, you may need to reference the full documentation:

- **Core extension docs**: `src/Extension/README.md`
- **UI system docs**: `src/Extension/UI/README.md`

Read these files when you need detailed information about specific APIs, advanced patterns, or complete reference material.

---

## Creating a New Extension

1. Create a PHP class implementing `ExtensionInterface`
2. Return a unique extension name via `name()` method
3. Implement `register(ExtensionApi $api)` to add functionality
4. Add the extension to `.maestro/settings.json` for manual registration, or use auto-discovery

### ExtensionApi Methods

| Method | Purpose |
|--------|---------|
| `registerTool(ToolInterface $tool)` | Register an AI tool the agent can use |
| `registerCommand(InlineCommand $command)` | Register an inline command (e.g., `/status`) |
| `registerRenderer(string $toolName, ToolRenderer $renderer)` | Register a custom tool output renderer |
| `registerMemory(string $key, string $filePath)` | Register a memory file for agent context |
| `registerWidget(WidgetInterface $widget)` | Register a UI widget |
| `on(string $event, callable $handler)` | Register an event callback |
| `ui()` | Get the `UiBuilder` for UI customization |
| `tools()`, `commands()`, `renderers()`, `events()`, `memories()` | Get registries for advanced registration |

---

## Registering AI Tools

Tools are AI-accessible functions that extend what the agent can do. Use the `ToolInterface` from NeuronAI.

```php
use NeuronAI\Tools\ToolInterface;

class MyTool implements ToolInterface
{
    public function name(): string
    {
        return 'my_tool';
    }

    public function description(): string
    {
        return 'Description of what the tool does';
    }

    public function parameters(): array
    {
        return [
            'param1' => ['type' => 'string', 'description' => 'First parameter'],
            'param2' => ['type' => 'integer', 'description' => 'Second parameter'],
        ];
    }

    public function execute(array $args): string
    {
        // Tool logic here
        return 'Result';
    }
}

// Register in extension:
$api->registerTool(new MyTool());
```

---

## Creating Inline Commands

Inline commands are available in the interactive console with a `/` prefix.

```php
use NeuronCore\Maestro\Console\Inline\InlineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MyCommand implements InlineCommand
{
    public function getName(): string
    {
        return 'my-command';
    }

    public function getDescription(): string
    {
        return 'My custom command description';
    }

    public function execute(string $args, InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Hello from my command!');
    }
}

// Register in extension:
$api->registerCommand(new MyCommand());
```

---

## UI Customization

Use `$api->ui()` to access the `UiBuilder` for customizing the terminal interface.

### Slots (Layout Regions)

Slots are named regions of terminal output. The four built-in slots are:

| Slot | Position | Line Ending |
|------|----------|-------------|
| `HEADER` | Before content | Followed by blank line |
| `CONTENT` | Main response area | Normal newline per item |
| `STATUS_BAR` | After content | No trailing newline |
| `FOOTER` | After status bar | Preceded by blank line |

```php
use NeuronCore\Maestro\Extension\Ui\SlotType;

// Add content to a slot (higher priority renders first)
$api->ui()->addToSlot(SlotType::HEADER, 'Project: my-app', priority: 900);
$api->ui()->addToSlot(SlotType::STATUS_BAR, ' ⎇ main ', priority: 700);
$api->ui()->addToSlot(SlotType::FOOTER, 'Tip: type /help', priority: 100);

// Clear all items in a slot (affects all extensions)
$api->ui()->clearSlot(SlotType::HEADER);
```

**Multiple extensions can add to the same slot** — items are appended and sorted by priority. Use high priority (e.g., 900) for top position, low priority (e.g., 100) for bottom.

### Themes

A theme defines colors, styles, and icons. Replaces the active theme globally.

```php
use NeuronCore\Maestro\Extension\Ui\ThemeInterface;
use NeuronCore\Maestro\Extension\Ui\ColorName;
use NeuronCore\Maestro\Extension\Ui\StyleName;
use NeuronCore\Maestro\Extension\Ui\IconName;

class MyTheme implements ThemeInterface
{
    public function name(): string
    {
        return 'my-theme';
    }

    public function color(ColorName $color): string
    {
        return match ($color) {
            ColorName::PRIMARY => 'blue',
            ColorName::SUCCESS => 'green',
            ColorName::WARNING => 'yellow',
            ColorName::ERROR   => 'red',
            ColorName::INFO    => 'cyan',
            ColorName::MUTED   => 'gray',
            ColorName::ACCENT  => 'magenta',
        };
    }

    public function style(StyleName $style): string
    {
        return match ($style) {
            StyleName::BOLD      => 'options=bold',
            StyleName::DIM       => 'options=dim',
            StyleName::UNDERLINE => 'options=underscore',
            StyleName::DEFAULT   => '',
        };
    }

    public function icon(IconName $icon): string
    {
        return match ($icon) {
            IconName::SUCCESS     => '✔',
            IconName::ERROR       => '✘',
            IconName::WARNING     => '▲',
            IconName::INFO        => '●',
            IconName::SPINNER     => '◌',
            IconName::ARROW_RIGHT => '→',
            IconName::ARROW_DOWN  => '↓',
            IconName::DOT         => '·',
        };
    }
}

// Register in extension (replaces current theme globally):
$api->ui()->registerTheme(new MyTheme());
```

### Widgets

Widgets are reusable components that render structured data. They must be invoked by rendering code — registering a widget makes it available, it doesn't auto-render.

```php
use NeuronCore\Maestro\Extension\Ui\WidgetInterface;
use NeuronCore\Maestro\Extension\Ui\ContentType;
use NeuronCore\Maestro\Extension\Ui\UiBuilder;

class MyWidget implements WidgetInterface
{
    public function name(): string
    {
        return 'my_widget';
    }

    public function contentType(): ContentType
    {
        return ContentType::STATUS;
    }

    public function render(array $data, UiBuilder $ui): string
    {
        $icon = $ui->theme()->icon(IconName::SUCCESS);
        return $ui->formatText("{$icon} My widget output", ColorName::SUCCESS);
    }
}

// Register in extension:
$api->registerWidget(new MyWidget());
```

**Note**: Widget names must be unique. Registering with an existing name replaces the previous widget. To extend rather than replace, use a unique name.

### Formatting Text

Use `formatText()` to apply colors and styles using the active theme:

```php
use NeuronCore\Maestro\Extension\Ui\ColorName;
use NeuronCore\Maestro\Extension\Ui\StyleName;
use NeuronCore\Maestro\Extension\Ui\IconName;

// Color only
$line = $api->ui()->formatText('text', ColorName::PRIMARY);

// Style only
$line = $api->ui()->formatText('text', style: StyleName::BOLD);

// Color + style
$line = $api->ui()->formatText('text', ColorName::PRIMARY, StyleName::BOLD);

// With theme icon
$icon = $api->ui()->theme()->icon(IconName::SUCCESS);
$line = $api->ui()->formatText("{$icon} Done", ColorName::SUCCESS);
```

---

## Memory Files

Memory files are automatically loaded and injected into the agent's system prompt, providing project-specific context.

```php
// Register a memory file bundled with your extension
$api->registerMemory('my-extension.guidelines', __DIR__ . '/memory/guidelines.md');
```

Requirements:
- File must exist and be readable
- Use absolute paths (`__DIR__` for files within your extension)
- Content must be Markdown

Memory files are presented to the agent with the key you provided (e.g., `### my-extension.guidelines`).

---

## Event Handling

Extensions can react to application events:

```php
use NeuronCore\Maestro\Events\AgentThinkingEvent;
use NeuronCore\Maestro\Events\AgentResponseEvent;
use NeuronCore\Maestro\Events\ToolApprovalRequestedEvent;

// Before AI thinks
$api->on(AgentThinkingEvent::class, function ($event, $context) {
    // Access event properties
});

// After AI responds
$api->on(AgentResponseEvent::class, function ($event, $context) {
    // Handle response
});

// When tool approval is requested
$api->on(ToolApprovalRequestedEvent::class, function ($event, $context) {
    // Handle approval request
});
```

---

## Configuration Handling

Extensions can accept configuration from `.maestro/settings.json`:

```json
{
    "extensions": [
        {
            "class": "MyVendor\\MyExtension\\MyExtension",
            "enabled": true,
            "config": {
                "api_key": "your-api-key",
                "option": "value"
            }
        }
    ]
}
```

```php
class MyExtension implements ExtensionInterface
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function register(ExtensionApi $api): void
    {
        $apiKey = $this->config['api_key'] ?? null;
        $option = $this->config['option'] ?? 'default';
        // Use config values
    }
}
```

---

## Packaging as Composer Package

Create a `composer.json` for your extension:

```json
{
    "name": "my-vendor/my-extension",
    "type": "library",
    "description": "My Maestro extension",
    "require": {
        "neuron-core/maestro": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "MyVendor\\MyExtension\\": "src/"
        }
    }
}
```

### Auto-Discovery Setup

Add the `extra.maestro` field to enable automatic extension discovery:

```json
{
    "name": "my-vendor/my-extension",
    "type": "library",
    "require": {
        "neuron-core/maestro": "^1.0"
    },
    "extra": {
        "maestro": {
            "extensions": [
                "MyVendor\\MyExtension\\MyExtension"
            ]
        }
    },
    "autoload": {
        "psr-4": {
            "MyVendor\\MyExtension\\": "src/"
        }
    }
}
```

A single package can declare multiple extensions:

```json
{
    "extra": {
        "maestro": {
            "extensions": [
                "MyVendor\\Package\\CoreExtension",
                "MyVendor\\Package\\AdminExtension",
                "MyVendor\\Package\\ReportingExtension"
            ]
        }
    }
}
```

### Installation

Users install the extension with:

```bash
composer require my-vendor/my-extension
```

After installation, run `composer dump-autoload` or `maestro discover` to trigger auto-discovery.

### Disabling Auto-Discovered Extensions

Users can disable extensions in `.maestro/settings.json`:

```json
{
    "extensions": {
        "MyVendor\\Package\\CoreExtension": {
            "enabled": false
        },
        "MyVendor\\Package\\AdminExtension": {
            "enabled": true,
            "config": {
                "api_key": "your-api-key"
            }
        }
    }
}
```

---

## Manual Registration

Extensions can also be registered manually without auto-discovery:

```json
{
    "extensions": [
        {
            "class": "MyVendor\\AnotherExtension\\CustomExtension",
            "enabled": true,
            "config": {
                "option": "value"
            }
        }
    ]
}
```

---

## Code Style Guidelines

When writing Maestro extensions, follow these conventions:

- Use `protected` visibility for non-public properties and methods (not `private`)
- Never define classes as `final`
- Prefer strict typing - use type hints for properties, parameters, and return values
- Use descriptive method names that follow PSR conventions
- Organize extension code with clear separation between registration and implementation

---

## Common Extension Patterns

### Tool with Configuration

```php
class ConfiguredTool implements ToolInterface
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function execute(array $args): string
    {
        $apiKey = $this->config['api_key'] ?? null;
        // Use config in tool logic
    }
}

// Register in extension:
$api->registerTool(new ConfiguredTool($this->config));
```

### Event-Driven Renderer

```php
class MyExtension implements ExtensionInterface
{
    public function register(ExtensionApi $api): void
    {
        $api->on(AgentResponseEvent::class, function ($event, $context) use ($api) {
            $response = $event->getMessage()->content ?? '';
            if (str_contains($response, '[deploy_status]')) {
                $widget = $api->ui()->widgets()->get('deploy_status');
                if ($widget) {
                    $statusBar = $api->ui()->widgets()->render($widget, ['environment' => 'prod']);
                    $api->ui()->addToSlot(SlotType::STATUS_BAR, $statusBar, priority: 800);
                }
            }
        });
    }
}
```

---

## Testing Your Skills

When creating code for users:
1. Read the relevant documentation files for detailed API reference
2. Follow the code style guidelines (protected visibility, no final classes, strict typing)
3. Provide complete, working examples that demonstrate the pattern
4. Explain how to register and configure the extension
5. Include the necessary `use` statements in code examples

The skill should produce code that works immediately when copied into a Maestro project with the appropriate dependencies installed.
