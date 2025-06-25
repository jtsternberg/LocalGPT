<?php

namespace tests\Command;

use LocalGPT\Command\ListModelsCommand;
use LocalGPT\Provider\AnthropicProvider;
use LocalGPT\Provider\GeminiProvider;
use LocalGPT\Provider\OllamaProvider;
use LocalGPT\Provider\OpenAIProvider;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ListModelsCommandTest extends TestCase
{
	public function testExecute()
	{
		// Mock providers
		$geminiProviderMock = $this->createMock(GeminiProvider::class);
		$geminiProviderMock->method('listModels')->willReturn(['gemini-model-1', 'gemini-model-2']);

		$openAIProviderMock = $this->createMock(OpenAIProvider::class);
		$openAIProviderMock->method('listModels')->willReturn(['openai-model-1', 'openai-model-2']);

		$anthropicProviderMock = $this->createMock(AnthropicProvider::class);
		$anthropicProviderMock->method('listModels')->willThrowException(new \Exception('Anthropic Error'));

		$ollamaProviderMock = $this->createMock(OllamaProvider::class);
		$ollamaProviderMock->method('listModels')->willReturn([]); // Test empty models case

		// Mock ProviderFactory
		$providerFactoryMock = $this->createMock(ProviderFactory::class);
		$providerFactoryMock->method('createProviderByName')
			->willReturnMap([
				['gemini', $geminiProviderMock],
				['openai', $openAIProviderMock],
				['anthropic', $anthropicProviderMock],
				['ollama', $ollamaProviderMock],
			]);

		$configServiceMock = $this->createMock(ConfigService::class);

		// Create command with mock factory
		$command = new ListModelsCommand($providerFactoryMock, $configServiceMock);

		$commandTester = new CommandTester($command);
		$commandTester->execute([]);

		$output = $commandTester->getDisplay();
		$this->assertStringContainsString('Gemini Models', $output);
		$this->assertStringContainsString('gemini-model-1', $output);
		$this->assertStringContainsString('Openai Models', $output);
		$this->assertStringContainsString('openai-model-2', $output);
		$this->assertStringContainsString('Anthropic: Anthropic Error', $output);
		$this->assertStringNotContainsString('Ollama Models', $output);
	}
}