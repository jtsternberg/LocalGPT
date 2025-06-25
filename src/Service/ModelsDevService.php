<?php

namespace LocalGPT\Service;

class ModelsDevService
{
	private const API_URL = 'https://models.dev/api.json';
	private string $cacheDir;
	private string $cacheFile;

	public function __construct(?string $cacheDir = null)
	{
		if (!$cacheDir) {
			$homeDir = getenv('HOME');

			if (false === $homeDir) {
				$homeDir = getenv('USERPROFILE');
			}

			if (false !== $homeDir) {
				$cacheDir = rtrim($homeDir, '/\\') . '/.config/localgpt/cache';
			} else {
				$cacheDir  = getcwd() . '/.cache';
			}
		}

		$this->cacheDir = $cacheDir;

		$this->cacheFile = $this->cacheDir . '/models.dev.json';
		if (! is_dir($this->cacheDir)) {
			mkdir($this->cacheDir, 0755, true);
		}
	}

	public function getModels(): array
	{
		$data = $this->getCached();
		if (null === $data) {
			$data = $this->fetchAndCache();
		}

		return $data;
	}

	protected function getCached(): ?array
	{
		$data = null;

		// Cache for 7 days
		$canUseCache = file_exists($this->cacheFile) && (time() - filemtime($this->cacheFile) < 604800);

		if ($canUseCache) {
			$data = json_decode(file_get_contents($this->cacheFile), true);
			if ( JSON_ERROR_NONE !== json_last_error() ) {
				throw new \RuntimeException("Failed to parse cached data from {$this->cacheFile}. Error: " . json_last_error_msg());
			}
		}

		return $data;
	}

	protected function fetchAndCache(): array
	{
		$url = self::API_URL;
		$response = $this->fetchData($url);
		$data     = json_decode($response, true);

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			throw new \RuntimeException("Failed to parse fetched data from {$url}. Error: " . json_last_error_msg());
		}

		$providers = ProviderFactory::SUPPORTED_PROVIDERS;
		foreach ($providers as $providerId => $providerClass) {
			$providers[$providerId] = $providerId;
		}

		$providers['google'] = 'gemini';

		$models = [];
		foreach ($data as $providerId => $providerData) {
			if (isset($providers[$providerId])) {
				$ourProviderId = $providers[$providerId];
				$models[$ourProviderId] = $providerData;
			}
		}

		$this->storeCache($models);

		return $models;
	}

	protected function fetchData(string $url): string
	{
		$response = file_get_contents($url);
		if ($response === false) {
			throw new \RuntimeException("Failed to fetch data from {$url}");
		}

		return $response;
	}

	protected function storeCache(array $data): void
	{
		file_put_contents($this->cacheFile, json_encode($data, JSON_PRETTY_PRINT));
	}

	public function getProviderModels(string $providerId): array
	{
		$data = $this->getModels();
		return $data[$providerId]['models'] ?? [];
	}

	public function findModel(string $modelId): ?array
	{
		$data = $this->getModels();
		foreach ($data as $providerId => $providerData) {
			foreach (($providerData['models'] ?? []) as $model) {
				if ($model['id'] === $modelId) {
					return [$model, $providerId];
				}
			}
		}

		return null;
	}
}