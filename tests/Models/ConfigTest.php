<?php

namespace LocalGPT\Tests\Models;

use LocalGPT\Models\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
	private $config;
	private $testDir;
	private $configData;

	protected function setUp(): void
	{
		$this->testDir = sys_get_temp_dir() . '/test-gpt-model';
		if (!is_dir($this->testDir)) {
			mkdir($this->testDir, 0777, true);
		}

		$systemPromptPath = $this->testDir . '/SYSTEM_PROMPT.md';
		file_put_contents($systemPromptPath, 'You are a helpful assistant.');

		$referenceDir = $this->testDir . '/reference-files';
		if (!is_dir($referenceDir)) {
			mkdir($referenceDir, 0777, true);
		}
		$referenceFilePath = $referenceDir . '/ref1.txt';
		file_put_contents($referenceFilePath, 'This is a reference file.');

		$this->configData = [
			'path' => $this->testDir,
			'provider' => 'test-provider',
			'model' => 'test-model',
			'system_prompt' => './SYSTEM_PROMPT.md',
			'reference_files' => [
				'./reference-files/ref1.txt'
			],
			'custom_key' => 'custom_value',
		];

		$this->config = new Config($this->configData);
	}

	protected function tearDown(): void
	{
		$this->deleteDirectory($this->testDir);
	}

	private function deleteDirectory($dir)
	{
		if (!file_exists($dir)) {
			return true;
		}

		if (!is_dir($dir)) {
			return unlink($dir);
		}

		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}

			if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
				return false;
			}
		}

		return rmdir($dir);
	}

	public function testConstructorThrowsExceptionForInvalidConfig()
	{
		$this->expectException(\InvalidArgumentException::class);
		new Config([]);
	}

	public function testGetters()
	{
		$this->assertEquals($this->testDir, $this->config->getName());
		$this->assertEquals($this->testDir, $this->config->getPath());
		$this->assertEquals('test-provider', $this->config->getProvider());
		$this->assertEquals('test-model', $this->config->getModel());
		$this->assertEquals('./SYSTEM_PROMPT.md', $this->config->getSystemPrompt());
		$this->assertEquals(['./reference-files/ref1.txt'], $this->config->getReferenceFiles());
		$this->assertEquals('custom_value', $this->config->get('custom_key'));
		$this->assertNull($this->config->get('nonexistent_key'));
	}

	public function testGetSystemPromptText()
	{
		$this->assertEquals('You are a helpful assistant.', $this->config->getSystemPromptText());
	}

	public function testGetReferenceFilesWithContent()
	{
		$files = $this->config->getReferenceFilesWithContent();
		$this->assertCount(1, $files);
		$this->assertEquals(realpath($this->testDir . '/reference-files/ref1.txt'), realpath($files[0]['path']));
		$this->assertEquals('This is a reference file.', $files[0]['content']);
	}

	public function testGetReferenceFilesWithContentHandlesMissingFile()
	{
		$configData = $this->configData;
		$configData['reference_files'][] = './reference-files/nonexistent.txt';
		$config = new Config($configData);

		$files = $config->getReferenceFilesWithContent();
		// Should still only count the one that exists.
		$this->assertCount(1, $files);
		$this->assertEquals(realpath($this->testDir . '/reference-files/ref1.txt'), realpath($files[0]['path']));
	}

	public function testMagicMethods()
	{
		// Test __get
		$this->assertEquals('test-model', $this->config->model);
		$this->assertEquals($this->testDir, $this->config->name);
		$this->assertNull($this->config->nonexistent);

		// Test __set
		$this->config->new_key = 'new_value';
		$this->assertEquals('new_value', $this->config->get('new_key'));
		$this->assertEquals('new_value', $this->config->new_key);
	}
}