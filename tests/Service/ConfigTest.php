<?php

namespace LocalGPT\Tests\Service;

use LocalGPT\Service\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
	private $config;
	private $testGptName = 'test-gpt';
	private $testDir;

	protected function setUp(): void
	{
		$this->testDir = sys_get_temp_dir() . '/' . $this->testGptName;
		if (!is_dir($this->testDir)) {
			mkdir($this->testDir, 0777, true);
		}
		chdir($this->testDir);

		// Create a dummy .env file for testing getApiKey
		file_put_contents(sys_get_temp_dir() . '/.env', "GEMINI_API_KEY=test-key\n");

		// Define a mock for the Dotenv::createImmutable part or the global constant
		if (!defined('LOCALGPT_BASE_PATH')) {
			define('LOCALGPT_BASE_PATH', sys_get_temp_dir());
		}

		$this->config = new Config();
	}

	protected function tearDown(): void
	{
		// Clean up created files and directories
		$this->deleteDirectory($this->testDir);
		$envFile = sys_get_temp_dir() . '/.env';
		if (file_exists($envFile)) {
			unlink($envFile);
		}
		chdir(dirname(__DIR__)); // Go back to the original directory
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

	public function testGetApiKey()
	{
		$this->assertEquals('test-key', $this->config->getApiKey('gemini'));
		$this->assertNull($this->config->getApiKey('nonexistent'));
	}

	public function testGetOrCreateConfigDir()
	{
		$configDir = $this->config->getOrCreateConfigDir($this->testGptName);
		$this->assertDirectoryExists($configDir);
		$this->assertEquals(realpath($this->testDir . '/' . $this->testGptName), realpath($configDir));
	}

	public function testGetOrCreateReferenceDir()
	{
		$referenceDir = $this->config->getOrCreateReferenceDir($this->testGptName);
		$this->assertDirectoryExists($referenceDir);
		$this->assertEquals(realpath($this->testDir . '/' . $this->testGptName . '/reference-files'), realpath($referenceDir));
	}

	public function testSaveAndLoadGptConfig()
	{
		$configData = ['model' => 'test-model', 'provider' => 'test-provider'];
		$this->config->saveGptConfig($this->testGptName, $configData);

		$configPath = $this->testDir . '/' . $this->testGptName . '/gpt.json';
		$this->assertFileExists($configPath);

		$loadedConfig = $this->config->loadGptConfig($this->testGptName);
		$this->assertEquals('test-model', $loadedConfig['model']);
		$this->assertEquals('test-provider', $loadedConfig['provider']);
		$this->assertEquals(realpath($this->testDir . '/' . $this->testGptName), realpath($loadedConfig['path']));
	}

	public function testSaveSystemPrompt()
	{
		$prompt = 'This is a test system prompt.';
		$this->config->saveSystemPrompt($this->testGptName, $prompt);

		$promptPath = $this->config->getConfigDir($this->testGptName) . '/SYSTEM_PROMPT.md';
		$this->assertFileExists($promptPath);
		$this->assertEquals($prompt, file_get_contents($promptPath));
	}

	public function testSaveReferenceFile()
	{
		$this->config->saveGptConfig($this->testGptName, []);
		$referenceFilePath = $this->testDir . '/test-file.md';
		file_put_contents($referenceFilePath, 'test content');

		$newPath = $this->config->saveReferenceFile($this->testGptName, $referenceFilePath);
		$this->assertEquals('./reference-files/test-file.md', $newPath);

		$destinationPath = $this->config->getOrCreateReferenceDir($this->testGptName) . '/test-file.md';
		$this->assertFileExists($destinationPath);

		$loadedConfig = $this->config->loadGptConfig($this->testGptName);
		$this->assertContains('./reference-files/test-file.md', $loadedConfig['reference_files']);
	}

	public function testSaveReferenceFileThrowsExceptionForNonexistentFile()
	{
		$this->config->saveGptConfig($this->testGptName, []);
		$this->expectException(\InvalidArgumentException::class);
		$this->config->saveReferenceFile($this->testGptName, 'nonexistent-file.md');
	}

	public function testDeleteReferenceFile()
	{
		$configData = ['reference_files' => ['./reference-files/test-file.md']];
		$this->config->saveGptConfig($this->testGptName, $configData);

		$referenceDir = $this->config->getOrCreateReferenceDir($this->testGptName);
		$referenceFilePath = $referenceDir . '/test-file.md';
		file_put_contents($referenceFilePath, 'test content');

		$result = $this->config->deleteReferenceFile($this->testGptName, 'test-file.md');
		$this->assertTrue($result);
		$this->assertFileDoesNotExist($referenceFilePath);

		$loadedConfig = $this->config->loadGptConfig($this->testGptName);
		$this->assertEmpty($loadedConfig['reference_files']);
	}

	public function testDeleteReferenceFileThrowsExceptionForNonexistentFileInConfig()
	{
		$this->config->saveGptConfig($this->testGptName, ['reference_files' => []]);
		$this->expectException(\InvalidArgumentException::class);
		$this->config->deleteReferenceFile($this->testGptName, 'nonexistent-file.md');
	}
}