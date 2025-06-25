<?php

namespace LocalGPT\Models;

use LocalGPT\Service\Utils;

class Config
{
	protected $name;
	public function __construct(protected array $config)
	{
		if (empty($this->config['path'])) {
			$config = print_r($this->config, true);
			throw new \InvalidArgumentException("Configuration file invalid: {$config}");
		}
		$this->name = str_replace(getcwd() . '/', '', $this->config['path']);
	}

	public function get(string $key): mixed
	{
		return $this->config[$key] ?? null;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPath(): string
	{
		return $this->config['path'];
	}

	public function getProvider(): string
	{
		return $this->config['provider'];
	}

	public function getModel(): string
	{
		return $this->config['model'];
	}

	public function getSystemPrompt(): string
	{
		return $this->config['system_prompt'] ?? '';
	}

	public function getSystemPromptText(): string
	{
		$systemPrompt = $this->getSystemPrompt();
		if (empty($systemPrompt)) {
			return '';
		}

		$systemPrompt = file_get_contents(Utils::convertPathToAbsolute($systemPrompt, $this->getPath()));

		return trim($systemPrompt);
	}

	public function getReferenceFiles(): array
	{
		return $this->config['reference_files'] ?? [];
	}

	protected function getReferenceFileData(string $path): array
	{
		$absolutePath = Utils::convertPathToAbsolute($path, $this->getPath());
		if (is_file($absolutePath) && is_readable($absolutePath)) {
			return [
				'path' => $absolutePath,
				'content' => trim(file_get_contents($absolutePath)),
			];
		}

		return [];
	}

	public function getReferenceFilesWithContent(): array
	{
		$referenceFiles = $this->getReferenceFiles();
		$files          = [];
		if (!empty($referenceFiles)) {
			foreach ($referenceFiles as $path) {
				$data = $this->getReferenceFileData($path);
				if (!empty($data)) {
					$files[] = $data;
				}
			}
		}

		return $files;
	}

	public function __set( $key, $value ) {
		$this->config[$key] = $value;
	}

	public function __get( $key ) {
		if ('name' === $key) {
			return $this->name;
		}

		return $this->config[$key] ?? null;
	}
}