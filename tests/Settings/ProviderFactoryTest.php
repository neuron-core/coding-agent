<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Settings;

use NeuronAI\Providers\AIProviderInterface;
use NeuronCore\Maestro\Settings\ProviderFactory;
use NeuronCore\Maestro\Settings\ProviderFactoryInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ProviderFactoryTest extends TestCase
{
    private ProviderFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ProviderFactory();
    }

    public function testImplementsProviderFactoryInterface(): void
    {
        $this->assertInstanceOf(ProviderFactoryInterface::class, $this->factory);
    }

    /**
     * @dataProvider validAnthropicSettingsProvider
     */
    public function testCreateAnthropicProvider(array $providerConfig): void
    {
        $providers = ['anthropic' => $providerConfig];
        $provider = $this->factory->create('anthropic', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateAnthropicProviderWithDefaultSettings(): void
    {
        $providers = [
            'anthropic' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('anthropic', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateAnthropicProviderWithGlobalApiKey(): void
    {
        $providers = [
            'anthropic' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('anthropic', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateAnthropicProviderMissingApiKeyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Anthropic API key is not configured');

        $this->factory->create('anthropic', ['anthropic' => []]);
    }

    public function testCreateOpenAIProvider(): void
    {
        $providers = [
            'openai' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('openai', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateOpenAIProviderMissingApiKeyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('OpenAI API key is not configured');

        $this->factory->create('openai', ['openai' => []]);
    }

    public function testCreateGeminiProvider(): void
    {
        $providers = [
            'gemini' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('gemini', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateGeminiProviderMissingApiKeyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gemini API key is not configured');

        $this->factory->create('gemini', ['gemini' => []]);
    }

    public function testCreateCohereProvider(): void
    {
        $providers = [
            'cohere' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('cohere', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateCohereProviderMissingApiKeyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cohere API key is not configured');

        $this->factory->create('cohere', ['cohere' => []]);
    }

    public function testCreateMistralProvider(): void
    {
        $providers = [
            'mistral' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('mistral', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateMistralProviderMissingApiKeyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Mistral API key is not configured');

        $this->factory->create('mistral', ['mistral' => []]);
    }

    public function testCreateOllamaProvider(): void
    {
        $providers = [
            'ollama' => [
                'model' => 'llama2',
            ],
        ];

        $provider = $this->factory->create('ollama', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateOllamaProviderWithDefaults(): void
    {
        $providers = ['ollama' => []];

        $provider = $this->factory->create('ollama', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateGrokProvider(): void
    {
        $providers = [
            'xai' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('xai', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateGrokProviderUsingGrokAlias(): void
    {
        $providers = [
            'grok' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('grok', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateGrokProviderMissingApiKeyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('xAI API key is not configured');

        $this->factory->create('xai', ['xai' => []]);
    }

    public function testCreateDeepseekProvider(): void
    {
        $providers = [
            'deepseek' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('deepseek', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public function testCreateDeepseekProviderMissingApiKeyThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Deepseek API key is not configured');

        $this->factory->create('deepseek', ['deepseek' => []]);
    }

    public function testCreateUnknownProviderThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown provider "unknown"');

        $this->factory->create('unknown', ['unknown' => []]);
    }

    public function testCreateWithNoProviderTypeThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown provider');

        $this->factory->create('nonexistent', []);
    }

    public function testRegisterCustomProvider(): void
    {
        $mockProvider = $this->createMock(AIProviderInterface::class);

        $this->factory->register('custom', fn (): \PHPUnit\Framework\MockObject\MockObject => $mockProvider);

        $provider = $this->factory->create('custom', ['custom' => []]);

        $this->assertSame($mockProvider, $provider);
    }

    public function testRegisterCustomProviderOverwritesDefault(): void
    {
        $mockProvider = $this->createMock(AIProviderInterface::class);

        $this->factory->register('anthropic', fn (): \PHPUnit\Framework\MockObject\MockObject => $mockProvider);

        $providers = [
            'anthropic' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('anthropic', $providers);

        $this->assertSame($mockProvider, $provider);
    }

    public function testProviderNameIsCaseInsensitive(): void
    {
        $providers = [
            'anthropic' => [
                'api_key' => 'test-key',
            ],
        ];

        $provider = $this->factory->create('ANTHROPIC', $providers);

        $this->assertInstanceOf(AIProviderInterface::class, $provider);
    }

    public static function validAnthropicSettingsProvider(): array
    {
        return [
            'minimal' => [['api_key' => 'test-key']],
            'with_model' => [['api_key' => 'test-key', 'model' => 'claude-3-opus']],
            'with_max_tokens' => [['api_key' => 'test-key', 'max_tokens' => 4096]],
            'complete' => [[
                'api_key' => 'test-key',
                'model' => 'claude-3-opus',
                'max_tokens' => 4096,
            ]],
        ];
    }
}
