<?php

namespace LocalGPT\Tests\Provider;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Provider\AnthropicProvider;
use LLPhant\Chat\AnthropicChat;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AnthropicProviderTest extends TestCase
{
    private MockObject|AnthropicChat $clientMock;
    private MockObject|GptConfig $configMock;
    private AnthropicProvider $provider;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(AnthropicChat::class);
        $this->configMock = $this->createMock(GptConfig::class);

        $this->provider = new AnthropicProvider('test-api-key', $this->clientMock);
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