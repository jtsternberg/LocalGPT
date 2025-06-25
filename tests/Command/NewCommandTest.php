<?php

namespace tests\Command;

use LocalGPT\Command\NewCommand;
use LocalGPT\Provider\GeminiProvider;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ModelsDevService;
use LocalGPT\Service\ProviderFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class NewCommandTest extends TestCase
{
	private $configServiceMock;
	private $providerFactoryMock;
	private $modelsDevServiceMock;
	private $command;
	private $commandTester;

	protected function setUp(): void
	{
		$this->configServiceMock = $this->createMock(ConfigService::class);
		$this->providerFactoryMock = $this->createMock(ProviderFactory::class);
		$this->modelsDevServiceMock = $this->createMock(ModelsDevService::class);
		$this->command = new NewCommand($this->configServiceMock, $this->providerFactoryMock, $this->modelsDevServiceMock);
		$this->commandTester = new CommandTester($this->command);
	}

	public function testExecute()
	{
		$gptName = 'test-gpt';
		$configPath = '/path/to/test-gpt';

		// Mock provider and models
		$geminiProviderMock = $this->createMock(GeminiProvider::class);
		$geminiProviderMock->method('listModels')->willReturn(['gemini-pro', 'gemini-flash']);
		$geminiProviderMock->method('getDefaultModel')->willReturn('gemini-pro');

		$this->providerFactoryMock->method('createProviderByName')->willReturn($geminiProviderMock);

		$this->modelsDevServiceMock->method('getProviderModels')->willReturn([]);

		// Mock ConfigService methods
		$this->configServiceMock->method('createGptConfig')->with($gptName)->willReturn(['path' => $configPath]);
		$this->configServiceMock->expects($this->once())->method('saveGptConfig');
		$this->configServiceMock->expects($this->once())->method('saveSystemPrompt')->willReturn($this->configServiceMock);

		$this->commandTester->setInputs([
			'Test GPT',         // Title
			'A test GPT.',      // Description
			'gemini',           // Provider
			'gemini-pro',       // Model
			"You are a test.\nend", // System Prompt
		]);

		$this->commandTester->execute(['name' => $gptName]);

		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('New GPT Configuration', $output);
		$this->assertStringContainsString('GPT configuration created successfully at ' . $configPath, $output);
	}
}