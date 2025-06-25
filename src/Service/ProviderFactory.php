<?php

namespace LocalGPT\Service;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Provider\AnthropicProvider;
use LocalGPT\Provider\GeminiProvider;
use LocalGPT\Provider\OpenAIProvider;
use LocalGPT\Provider\OllamaProvider;
use LocalGPT\Provider\ProviderInterface;

class ProviderFactory
{
	public const SUPPORTED_PROVIDERS = [
		'gemini'    => GeminiProvider::class,
		'openai'    => OpenAIProvider::class,
		'anthropic' => AnthropicProvider::class,
		'ollama'    => OllamaProvider::class,
	];

	private ConfigService $config;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}

	public function createProvider(GptConfig $gptConfig): ?ProviderInterface
	{
		$provider = $this->createProviderByName($gptConfig->getProvider());
		$provider->setConfig($gptConfig);

		return $provider;
	}

	public function createProviderByName(string $providerName): ?ProviderInterface
	{
		$providerApiKey = $this->getProviderApiKey($providerName);
		$providerClass  = $this->getProviderClass($providerName);

		return new $providerClass($providerApiKey);
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
		// Ollama does not need an API key.
		if ($providerName === 'ollama') {
			return $providerName;
		}

		$apiKey = $this->config->getApiKey($providerName);

		if (!$apiKey) {
			throw new \Exception("API key for {$providerName} not found in .env file.");
		}
		return $apiKey;
	}
}