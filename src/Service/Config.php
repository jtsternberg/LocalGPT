<?php

namespace LocalGPT\Service;

use Dotenv\Dotenv;

class Config
{
	protected $basePath;
	protected static $env;

	public function __construct()
	{
		$this->basePath = getcwd();
		self::$env = self::$env ?? Dotenv::createImmutable(LOCALGPT_BASE_PATH)->load();
	}

	public function getOrCreateConfigDir(string $gptName): string
	{
		$configDir = $this->getConfigDir($gptName);
		if (!is_dir($configDir)) {
			mkdir($configDir, 0777, true);
		}
		return $configDir;
	}

	public function getConfigDir(string $gptName): string
	{
		return $this->basePath . '/' . $gptName;
	}

	public function getOrCreateReferenceDir(string $gptName): string
	{
		$referenceDir = $this->getConfigDir($gptName) . '/reference-files';
		if (!is_dir($referenceDir)) {
			mkdir($referenceDir, 0777, true);
		}
		return $referenceDir;
	}

	public function getConfigPath(string $gptName): string
	{
		return $this->getConfigDir($gptName) . '/gpt.json';
	}

	public function createGptConfig(string $gptName): array
	{
		$configPath = $this->getConfigPath($gptName);

		$config = [];
		$config['path'] = dirname($configPath);

		return $config;
	}

	public function loadGptConfig(string $gptName): array
	{
		$configPath = $this->getConfigPath($gptName);

		if (!file_exists($configPath)) {
			throw new \InvalidArgumentException("Configuration file not found for GPT: {$gptName}");
		}

		$configJson = file_get_contents($configPath);
		$config = json_decode($configJson, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \RuntimeException("Error parsing JSON from {$configPath}: " . json_last_error_msg());
		}

		$config['path'] = dirname($configPath);

		return $config;
	}

	public function saveGptConfig(string $gptName, array $config)
	{
		unset($config['path']);
		$this->getOrCreateConfigDir($gptName);
		$configPath = $this->getConfigPath($gptName);
		$json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		file_put_contents($configPath, $json);

		return $this;
	}

	public function saveSystemPrompt(string $gptName, string $systemPrompt)
	{
		$systemPromptPath = $this->getOrCreateConfigDir($gptName) . '/SYSTEM_PROMPT.md';
		file_put_contents($systemPromptPath, $systemPrompt);

		return $this;
	}

	public function saveReferenceFile(string $gptName, string $filePath): ?string
	{
		$config = $this->loadGptConfig($gptName);

		if (!file_exists($filePath)) {
			throw new \InvalidArgumentException("File not found: {$filePath}");
		}

		$referenceDir = $this->getOrCreateReferenceDir($gptName);

		$fileName = basename($filePath);
		$destinationPath = $referenceDir . '/' . $fileName;
		$relativePath = './reference-files/' . $fileName;

		if (isset($config['reference_files']) && in_array($relativePath, $config['reference_files'])) {
			throw new \InvalidArgumentException("Reference file already exists in config: {$relativePath}");
		}

		$copied = true;
		if (realpath($filePath) !== realpath($destinationPath)) {
			$copied = copy($filePath, $destinationPath);
		}

		if (!$copied) {
			throw new \RuntimeException("Failed to copy file to: {$destinationPath}");
		}

		$config['reference_files'][] = $relativePath;

		$this->saveGptConfig($gptName, $config);

		return $relativePath;
	}

	public function deleteReferenceFile(string $gptName, string $filePathToDelete): bool
	{
		$config = $this->loadGptConfig($gptName);

		// Cleanup.
		$prefix = './reference-files/';
		$filePathToDelete = $prefix . str_replace($prefix, '', $filePathToDelete);

		$key = array_search($filePathToDelete, $config['reference_files']);

		if ($key === false) {
			throw new \InvalidArgumentException("Reference file not found in config: {$filePathToDelete}");
		}

		$absolutePath = realpath(Utils::convertPathToAbsolute($filePathToDelete, $this->getConfigDir($gptName)));

		$result = false;
		if ($absolutePath && file_exists($absolutePath)) {
			$result = unlink($absolutePath);
			if (!$result) {
				throw new \RuntimeException("Failed to delete file: {$absolutePath}");
			}
		} else {
			throw new \RuntimeException("File not found on disk, removing from config: {$filePathToDelete}");
		}

		unset($config['reference_files'][$key]);

		// Re-index the array
		$config['reference_files'] = array_values($config['reference_files']);

		$this->saveGptConfig($gptName, $config);

		return $result;
	}

	public function getApiKey(string $provider): ?string
	{
		$keyName = strtoupper($provider) . '_API_KEY';
		return self::$env[$keyName] ?? null;
	}
}