<?php

namespace LocalGPT\Tests\Provider;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Provider\OpenAIProvider;
use LLPhant\Chat\OpenAIChat;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OpenAIProviderTest extends TestCase
{
	private MockObject|OpenAIChat $clientMock;
	private MockObject|GptConfig $configMock;
	private OpenAIProvider $provider;

	protected function setUp(): void
	{
		$this->clientMock = $this->createMock(OpenAIChat::class);
		$this->configMock = $this->createMock(GptConfig::class);

		$this->provider = new OpenAIProvider('test-api-key', $this->clientMock);
	}

	public function testChat()
	{
		$this->provider->setConfig($this->configMock);

		$this->configMock->method('getSystemPromptText')->willReturn('');
		$this->configMock->method('getReferenceFilesWithContent')->willReturn([]);

		$this->clientMock
			->expects($this->once())
			->method('generateText')
			->with('Hello')
			->willReturn('Hi there!');

		$messages = [
			['role' => 'user', 'parts' => [['text' => 'Hello']]],
		];

		$response = $this->provider->chat($messages);

		$this->assertEquals('Hi there!', $response);
	}
}