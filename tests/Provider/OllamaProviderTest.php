<?php

namespace LocalGPT\Tests\Provider;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Provider\OllamaProvider;
use LLPhant\Chat\OllamaChat;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OllamaProviderTest extends TestCase
{
	private MockObject|OllamaProvider $providerMock;
	private MockObject|OllamaChat $clientMock;
	private MockObject|GptConfig $configMock;

	protected function setUp(): void
	{
		$this->clientMock = $this->createMock(OllamaChat::class);
		$this->configMock = $this->createMock(GptConfig::class);

		// We will test the provider methods separately
		$this->providerMock = $this->getMockBuilder(OllamaProvider::class)
			->setConstructorArgs(['test-api-key', $this->clientMock])
			->onlyMethods(['getOllamaListOutput'])
			->getMock();

		$this->providerMock->setConfig($this->configMock);
	}

	public function testListModels()
	{
		$fakeOllamaOutput = [
			'NAME               ID           SIZE   MODIFIED',
			'llama3:latest      365c0bd3c000 4.7 GB 4 months ago',
			'test-model:latest  1234567890ab 1.2 GB 1 second ago',
		];

		$this->providerMock->method('getOllamaListOutput')->willReturn($fakeOllamaOutput);

		$models = $this->providerMock->listModels();

		$this->assertCount(2, $models);
		$this->assertEquals(['llama3:latest', 'test-model:latest'], $models);
	}

	public function testListModelsReturnsDefaultOnNoOutput()
	{
		$this->providerMock->method('getOllamaListOutput')->willReturn([]);

		$models = $this->providerMock->listModels();

		$this->assertNotEmpty($models);
		$this->assertContains('llama3', $models);
	}

	public function testChat()
	{
		$this->configMock->method('getSystemPromptText')->willReturn('');
		$this->configMock->method('getReferenceFilesWithContent')->willReturn([]);

		$this->clientMock
			->expects($this->once())
			->method('generateText')
			->with('Hello')
			->willReturn('Hi from Ollama!');

		$messages = [
			['role' => 'user', 'parts' => [['text' => 'Hello']]],
		];

		$response = $this->providerMock->chat($messages);

		$this->assertEquals('Hi from Ollama!', $response);
	}
}