<?php

namespace tests\Command;

use LocalGPT\Command\ReferenceCommand;
use LocalGPT\Service\Config as ConfigService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ReferenceCommandTest extends TestCase
{
	private $configServiceMock;
	private $command;
	private $commandTester;

	protected function setUp(): void
	{
		$this->configServiceMock = $this->createMock(ConfigService::class);
		$this->command = new ReferenceCommand($this->configServiceMock);
		$this->commandTester = new CommandTester($this->command);
	}

	public function testListReferenceFiles()
	{
		$this->configServiceMock->method('loadGptConfig')->willReturn([
			'name' => 'my-gpt',
			'path' => __DIR__,
			'reference_files' => [
				'./reference-files/file1.txt',
				'./reference-files/file2.txt',
			]
		]);

		$this->configServiceMock->method('getOrCreateReferenceDir')->willReturn(__DIR__ . '/my-gpt/reference-files');

		$this->commandTester->execute([
			'name' => 'my-gpt',
			'--list' => true,
		]);

		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('Reference files:', $output);
		$this->assertStringContainsString('file1.txt', $output);
		$this->assertStringContainsString('file2.txt', $output);
	}

	public function testListNoReferenceFiles()
	{
		$this->configServiceMock->method('loadGptConfig')->willReturn([
			'name' => 'my-gpt',
			'path' => __DIR__,
			'reference_files' => []
		]);

		$this->commandTester->execute([
			'name' => 'my-gpt',
			'--list' => true,
		]);

		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('No reference files found.', $output);
	}

	public function testAddReferenceFile()
	{
		$this->configServiceMock->method('loadGptConfig')
			->willReturnOnConsecutiveCalls(
				[
					'name' => 'my-gpt',
					'path' => __DIR__,
					'reference_files' => []
				],
				[
					'name' => 'my-gpt',
					'path' => __DIR__,
					'reference_files' => ['./reference-files/new-file.txt']
				]
			);
		$this->configServiceMock->method('saveReferenceFile')->willReturn('./reference-files/new-file.txt');
		$this->configServiceMock->method('getOrCreateReferenceDir')->willReturn(__DIR__ . '/my-gpt/reference-files');


		$this->commandTester->execute([
			'name' => 'my-gpt',
			'file-path' => 'new-file.txt',
		]);

		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('Reference file added: ./reference-files/new-file.txt', $output);
		$this->assertStringContainsString('new-file.txt', $output);
	}

	public function testDeleteReferenceFile()
	{
		$this->configServiceMock->method('loadGptConfig')
			->willReturnOnConsecutiveCalls(
				[
					'name' => 'my-gpt',
					'path' => __DIR__,
					'reference_files' => ['./reference-files/file-to-delete.txt']
				],
				[
					'name' => 'my-gpt',
					'path' => __DIR__,
					'reference_files' => []
				]
			);
		$this->configServiceMock->method('deleteReferenceFile')->willReturn(true);
		$this->configServiceMock->method('getOrCreateReferenceDir')->willReturn(__DIR__ . '/my-gpt/reference-files');

		$this->commandTester->execute([
			'name' => 'my-gpt',
			'--delete' => 'file-to-delete.txt',
		]);

		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('Reference file removed: file-to-delete.txt', $output);
		$this->assertStringContainsString('No reference files found.', $output);
	}

	public function testNoOptions()
	{
		$this->configServiceMock->method('loadGptConfig')->willReturn([
			'name' => 'my-gpt',
			'path' => __DIR__,
			'reference_files' => []
		]);

		$this->commandTester->execute(['name' => 'my-gpt']);
		$output = $this->commandTester->getDisplay();
		$this->assertStringContainsString('Please provide a file to add, or use the --list or --delete options.', $output);
	}
}