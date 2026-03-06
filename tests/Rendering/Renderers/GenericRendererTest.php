<?php

declare(strict_types=1);

namespace NeuronCore\Maestro\Tests\Rendering\Renderers;

use NeuronCore\Maestro\Rendering\Renderers\GenericRenderer;
use NeuronCore\Maestro\Rendering\ToolRenderer;
use PHPUnit\Framework\TestCase;

class GenericRendererTest extends TestCase
{
    private GenericRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new GenericRenderer();
    }

    public function testImplementsToolRenderer(): void
    {
        $this->assertInstanceOf(ToolRenderer::class, $this->renderer);
    }

    public function testRenderFormatsOutput(): void
    {
        $result = $this->renderer->render('read_file', '{"file_path": "foo.php"}');

        $this->assertSame("● read_file( {\"file_path\": \"foo.php\"} )\n", $result);
    }

    public function testRenderWithEmptyArguments(): void
    {
        $result = $this->renderer->render('list_files', '');

        $this->assertSame("● list_files(  )\n", $result);
    }

    public function testRenderWithDifferentToolNames(): void
    {
        $result = $this->renderer->render('bash', 'ls -la');

        $this->assertSame("● bash( ls -la )\n", $result);
    }
}
