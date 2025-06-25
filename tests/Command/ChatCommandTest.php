<?php

namespace tests\Command;

use LocalGPT\Command\ChatCommand;
use LocalGPT\Provider\ProviderInterface;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ChatCommandTest extends TestCase
{
	private $configServiceMock;
	private $providerFactoryMock;
	private $command;
	private $commandTester;
	private $providerMock;

	protected function setUp(): void
	{
		$this->configServiceMock = $this->createMock(ConfigService::class);
		$this->providerFactoryMock = $this->createMock(ProviderFactory::class);
		$this->providerMock = $this->createMock(ProviderInterface::class);
		$this->command = new ChatCommand($this->configServiceMock, $this->providerFactoryMock, true);
		$this->commandTester = new CommandTester($this->command);
	}

	public function testHandleSingleMessage()
	{
		$this->configServiceMock->method('loadGptConfig')->willReturn([
			'name' => 'my-gpt',
			'title' => 'My GPT',
			'provider' => 'test-provider',
			'model' => 'test-model',
			'path' => __DIR__,
		]);

		$this->providerFactoryMock->method('createProvider')->willReturn($this->providerMock);

		$this->providerMock->method('chat')
			->with([['role' => 'user', 'parts' => [['text' => 'Hello']]]])
			->willReturn('Hi there!');

		$this->commandTester->execute([
			'name' => 'my-gpt',
			'--message' => 'Hello',
		]);

		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('Using GPT: My GPT', $output);
		$this->assertStringContainsString('Provider: test-provider', $output);
		$this->assertStringContainsString('Model: test-model', $output);
		$this->assertStringContainsString('Hi there!', $output);
	}

	public function testHandleMessageFile()
	{
		$this->configServiceMock->method('loadGptConfig')->willReturn([
			'name' => 'my-gpt',
			'title' => 'My GPT',
			'provider' => 'test-provider',
			'model' => 'test-model',
			'path' => __DIR__,
		]);

		$this->providerFactoryMock->method('createProvider')->willReturn($this->providerMock);

		// Create a temporary file
		$tempFile = tempnam(sys_get_temp_dir(), 'test');
		file_put_contents($tempFile, 'Hello from file');
		$this->providerMock->method('chat')
			->with([['role' => 'user', 'parts' => [['text' => 'Hello from file']]]])
			->willReturn('Hi there from file!');

		$this->commandTester->execute([
			'name' => 'my-gpt',
			'--messageFile' => $tempFile,
		]);

		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('Hi there from file!', $output);

		// Clean up the temporary file
		unlink($tempFile);
	}

	public function testHandleInteractiveChat()
	{
		$this->configServiceMock->method('loadGptConfig')->willReturn([
			'name' => 'my-gpt',
			'title' => 'My GPT',
			'provider' => 'test-provider',
			'model' => 'test-model',
			'path' => __DIR__,
		]);
		$this->providerFactoryMock->method('createProvider')->willReturn($this->providerMock);
		$this->providerMock->method('chat')
			->willReturnOnConsecutiveCalls('First response', 'Second response');

		$this->commandTester->setInputs(['First message', 'Second message', 'exit']);

		$this->commandTester->execute(['name' => 'my-gpt']);

		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('You can start chatting now.', $output);
		$this->assertStringContainsString('🤖 First response', $output);
		$this->assertStringContainsString('🤖 Second response', $output);
	}
}