<?php

declare(strict_types=1);

namespace NeuronCore\Synapse\Commands;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Exceptions\WorkflowException;
use NeuronAI\Workflow\Interrupt\ApprovalRequest;
use NeuronAI\Workflow\Interrupt\WorkflowInterrupt;
use NeuronCore\Synapse\Agent\CodingAgent;
use NeuronCore\Synapse\Rendering\ToolResultRendererRegistry;
use NeuronCore\Synapse\Settings\Settings;
use NeuronCore\Synapse\Settings\SettingsInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;
use Throwable;

use function in_array;
use function json_encode;
use function sprintf;
use function strtolower;
use function trim;
use function escapeshellarg;
use function passthru;
use function shell_exec;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function uniqid;
use function unlink;
use function readline;
use function fgets;
use function function_exists;

use const JSON_PRETTY_PRINT;
use const STDIN;

#[AsCommand(
    name: 'synapse',
    description: 'Synapse Coding Agent - built with Neuron AI framework',
)]
class SynapseCommand extends Command
{
    use CommandHelper;

    /**
     * @var string[] Array of action names allowed for the current session only
     */
    private array $sessionAllowedActions = [];

    /**
     * @var string[] Array of action names always allowed across sessions (from settings)
     */
    private array $alwaysAllowedActions = [];

    protected ?CodingAgent $agent = null;

    protected SettingsInterface $settings;

    private ToolResultRendererRegistry $rendererRegistry;

    protected OutputInterface $output;

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $this->rendererRegistry = ToolResultRendererRegistry::withDefaults();

        $this->settings = new Settings();

        if (!$this->settings->fileExists()) {
            $output->writeln('<error>Warning: Settings file not found at ' . $this->settings->getSettingsPath() . '</error>');
            $output->writeln('<error>The agent requires AI provider connection information.</error>');
            $output->writeln('');
            $output->writeln('<info>Create a settings.json file with your AI provider configuration:</info>');
            $output->writeln(json_encode([
                'provider' => [
                    'type' => 'openai',
                    'api_key' => 'your-api-key',
                    'model' => 'gpt-5',
                ],
            ], JSON_PRETTY_PRINT));
            $output->writeln('');
            return Command::FAILURE;
        }

        if (!$this->settings->hasValidProvider()) {
            $output->writeln('<error>Warning: Settings file is missing valid provider configuration.</error>');
            $output->writeln("<error>The 'provider.type' setting is required.</error>");
            $output->writeln('');
            return Command::FAILURE;
        }

        $this->interactiveMode();
        return Command::SUCCESS;
    }

    /**
     * @throws Throwable
     */
    protected function interactiveMode(): void
    {
        $this->agent = CodingAgent::make($this->settings);
        $this->alwaysAllowedActions = $this->settings->getAllowedTools();

        $this->output->writeln('<info>=== Synapse Coding Agent - built with Neuron AI framework ===</info>');
        $this->output->writeln("<info>Type 'exit' to end the conversation.</info>");
        $this->output->writeln('');

        while (true) {
            $userInput = trim($this->readInput('> '));

            if (in_array($userInput, ['', 'exit'], true)) {
                break;
            }

            $this->processUserInput($userInput);
        }

        $this->output->writeln('<info>Goodbye!</info>');
    }

    /**
     * @throws Throwable
     */
    protected function processUserInput(string $input): void
    {
        $this->output->writeln('');
        $this->output->write('Thinking...');

        try {
            $response = $this->agent->chat(new UserMessage($input))->getMessage();

            $this->clearOutput();
            $this->displayResponse($response->getContent() ?? 'No response received.');
        } catch (WorkflowInterrupt $interrupt) {
            $this->clearOutput();
            $this->handleWorkflowInterrupt($interrupt);
        } catch (Exception $e) {
            $this->output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            $this->output->writeln('');
        }
    }

    /**
     * @throws WorkflowInterrupt
     * @throws WorkflowException
     * @throws Throwable
     */
    protected function handleWorkflowInterrupt(WorkflowInterrupt $interrupt): void
    {
        /** @var ApprovalRequest $approvalRequest */
        $approvalRequest = $interrupt->getRequest();

        foreach ($approvalRequest->getPendingActions() as $action) {
            $rendered = $this->rendererRegistry->render($action->name, $action->description);

            if ($rendered !== null) {
                $this->output->write($rendered);
            } else {
                $this->output->writeln(sprintf('%s( %s )', $action->name, $action->description));
            }

            if (in_array($action->name, $this->alwaysAllowedActions, true)) {
                $action->approve();
                continue;
            }

            if (in_array($action->name, $this->sessionAllowedActions, true)) {
                $action->approve();
                continue;
            }

            $decision = $this->askDecision();
            $this->processDecision($action, $decision);
        }

        $this->output->write('Thinking...');
        try {
            $response = $this->agent->chat(interrupt: $approvalRequest)->getMessage();

            $this->clearOutput();
            $this->displayResponse($response->getContent() ?? 'No response received.');
        } catch (WorkflowInterrupt $nestedInterrupt) {
            $this->clearOutput();
            $this->handleWorkflowInterrupt($nestedInterrupt);
        }
    }

    /**
     * @return string The user's decision ('allow', 'session', 'always', or 'reject')
     */
    private function askDecision(): string
    {
        $this->output->writeln('');
        $this->output->writeln('Options:');
        $this->output->writeln('  1) Allow - Execute this action once (or Enter to process)');
        $this->output->writeln('  2) Session allow - Allow this tool for the current session');
        $this->output->writeln('  3) Always allow - Allow this tool permanently (saved to settings.json)');
        $this->output->writeln('  4) Reject - Do not execute this action');
        $this->output->writeln('');

        while (true) {
            $decision = strtolower(trim($this->readInput('Enter your choice (1/2/3/4):  ')));

            if (in_array($decision, ['', '1', 'allow'], true)) {
                return 'allow';
            }

            if (in_array($decision, ['2', 'session', 'session allow', 's'], true)) {
                return 'session';
            }

            if (in_array($decision, ['3', 'always', 'always allow', 'a'], true)) {
                return 'always';
            }

            if (in_array($decision, ['4', 'reject', 'no', 'n', 'r'], true)) {
                return 'reject';
            }

            $this->output->writeln('<error>Invalid choice. Please enter 1, 2, 3, or 4.</error>');
        }
    }

    private function processDecision(object $action, string $decision): void
    {
        if (in_array($decision, ['allow', 'session', 'always'], true)) {
            $action->approve();

            if ($decision === 'session') {
                $this->sessionAllowedActions[] = $action->name;
            } elseif ($decision === 'always') {
                $this->alwaysAllowedActions[] = $action->name;
                $this->sessionAllowedActions[] = $action->name;

                $this->agent->settings()->addAllowedTool($action->name);
                $this->output->writeln("<info>Tool '{$action->name}' is now always allowed (saved to settings.json).</info>");
            }
        } elseif ($decision === 'reject') {
            $action->reject();
        }

        $this->output->writeln('');
    }

    private function displayResponse(string $content): void
    {
        $this->output->writeln($content);
    }

    private function readInput(string $prompt): string
    {
        if (function_exists('readline')) {
            return (string) readline($prompt);
        }

        echo $prompt;
        return (string) fgets(STDIN);
    }
}
