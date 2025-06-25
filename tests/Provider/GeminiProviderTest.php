<?php

namespace LocalGPT\Tests\Provider;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Provider\GeminiProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;

class GeminiProviderTest extends TestCase
{
	private MockObject|GptConfig $configMock;
	private GeminiProvider $provider;

	protected function setUp(): void
	{
		$this->configMock = $this->createMock(GptConfig::class);
		$this->provider = new GeminiProvider('test-api-key');
		$this->provider->setConfig($this->configMock);
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
		$this->provider->setConfig($this->configMock);

		// Use reflection to set reference files, as setConfig is mocked
		$reflector = new \ReflectionProperty($this->provider, 'referenceFiles');
		$reflector->setAccessible(true);
		$reflector->setValue($this->provider, $referenceFiles);

		// Use reflection to access the protected method
		$method = new ReflectionMethod(GeminiProvider::class, 'buildSystemPrompt');
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