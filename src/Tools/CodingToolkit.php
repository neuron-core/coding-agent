<?php

declare(strict_types=1);

namespace NeuronCore\Synapse\Tools;

use NeuronCore\Synapse\Tools\Coding\BashTool;
use NeuronCore\Synapse\Tools\Coding\CreateFileTool;
use NeuronCore\Synapse\Tools\Coding\DeleteFileTool;
use NeuronCore\Synapse\Tools\Coding\EditFileTool;
use NeuronCore\Synapse\Tools\Coding\PatchFileTool;
use NeuronCore\Synapse\Tools\Coding\WriteFileTool;
use NeuronAI\Tools\Toolkits\AbstractToolkit;

/**
 * @method static static make()
 */
class CodingToolkit extends AbstractToolkit
{
    public function guidelines(): ?string
    {
        return 'Use these tools to modify files in the codebase and run shell commands.

**IMPORTANT**: Always read files first using read_file before proposing modifications to understand the current state.

Choose the appropriate tool:
- **create_file**: Create a new file (fails if file exists). Use for brand new files.
- **write_file**: Create or completely overwrite a file. Use when replacing entire file content.
- **edit_file**: Apply multiple search-and-replace operations. Use for targeted changes.
- **patch_file**: Apply unified diff patches. Use for complex multi-line changes.
- **delete_file**: Delete a file from the filesystem.
- **bash**: Execute a bash command. Use for running tests, build tools, linters, scripts, or any shell operation.

Each file tool returns a JSON-structured result with diff for user review. The diff will be displayed in the CLI and the user must approve before changes are applied.';
    }

    public function provide(): array
    {
        return [
            BashTool::make(),
            WriteFileTool::make(),
            EditFileTool::make(),
            PatchFileTool::make(),
            CreateFileTool::make(),
            DeleteFileTool::make(),
        ];
    }
}
