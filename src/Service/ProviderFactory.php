<?php

namespace LocalGPT\Service;

use LocalGPT\Provider\AnthropicProvider;
use LocalGPT\Provider\GeminiProvider;
use LocalGPT\Provider\OpenAIProvider;
use LocalGPT\Provider\ProviderInterface;

class ProviderFactory
{
	public const SUPPORTED_PROVIDERS = [
		'gemini' => GeminiProvider::class,
		'openai' => OpenAIProvider::class,
		'anthropic' => AnthropicProvider::class,
	];

	private Config $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function createProvider(string $providerName): ?ProviderInterface
	{
		$providerClass = $this->getProviderClass($providerName);
		return new $providerClass($this->getProviderApiKey($providerName));
	}

	public function getProviderClass(string $providerName): string
	{
		if (!isset(self::SUPPORTED_PROVIDERS[$providerName])) {
			throw new \Exception("Provider '{$providerName}' is not supported.");
		}

		return self::SUPPORTED_PROVIDERS[$providerName];
	}

	public function getProviderApiKey(string $providerName): string
	{
		$apiKey = $this->config->getApiKey($providerName);
		if (!$apiKey) {
			throw new \Exception("API key for {$providerName} not found in .env file.");
		}
		return $apiKey;
	}
}