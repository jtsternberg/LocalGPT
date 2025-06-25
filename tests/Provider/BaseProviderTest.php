<?php

namespace LocalGPT\Tests\Provider;

use LocalGPT\Provider\BaseProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class BaseProviderTest extends TestCase
{
    private BaseProvider $provider;

    protected function setUp(): void
    {
        // Create a concrete implementation of the abstract BaseProvider for testing
        $this->provider = new class('test-api-key') extends BaseProvider {
            public function listModels(): array { return []; }
        };
    }

    public function testBuildSystemPrompt()
    {
        // Set up test data
        $systemPrompt = 'You are a test assistant.';
        $referenceFiles = [
            ['path' => '/tmp/ref1.txt', 'content' => 'Reference content 1.'],
            ['path' => '/tmp/ref2.md', 'content' => 'Reference content 2.'],
        ];

        $this->provider->setSystemPrompt($systemPrompt);

        // Use reflection to set reference files
        $reflector = new \ReflectionProperty($this->provider, 'referenceFiles');
        $reflector->setAccessible(true);
        $reflector->setValue($this->provider, $referenceFiles);

        // Use reflection to access the protected method
        $method = new ReflectionMethod(BaseProvider::class, 'buildSystemPrompt');
        $method->setAccessible(true);

        $builtPrompt = $method->invoke($this->provider);

        // Assertions
        $this->assertStringContainsString($systemPrompt, $builtPrompt);
        $this->assertStringContainsString('--- REFERENCE MATERIALS ---', $builtPrompt);
        $this->assertStringContainsString('--- ref1.txt ---', $builtPrompt);
        $this->assertStringContainsString('Reference content 1.', $builtPrompt);
        $this->assertStringContainsString('--- ref2.md ---', $builtPrompt);
        $this->assertStringContainsString('Reference content 2.', $builtPrompt);
        $this->assertStringContainsString('--- SYSTEM PROMPT ---', $builtPrompt);
        $this->assertStringContainsString('The current date time is', $builtPrompt);
    }
}