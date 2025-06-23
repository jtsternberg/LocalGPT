<?php

namespace LocalGPT\Service;

use LocalGPT\Provider\GeminiProvider;
use LocalGPT\Provider\ProviderInterface;

class ProviderFactory
{
	public const SUPPORTED_PROVIDERS = [
		'gemini',
	];

	public const MODEL_DEFAULTS = [
		'gemini' => 'gemini-2.5-flash',
	];

	private Config $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function createProvider(string $providerName): ?ProviderInterface
	{
		if (!in_array($providerName, self::SUPPORTED_PROVIDERS)) {
			throw new \Exception("Provider '{$providerName}' is not supported.");
		}

		switch ($providerName) {
			case 'gemini':
				$apiKey = $this->config->getApiKey('gemini');
				if (!$apiKey) {
					throw new \Exception('API key for gemini not found in .env file.');
				}
				return new GeminiProvider($apiKey);
				// Add other providers here in the future
			default:
				throw new \Exception("Provider '{$providerName}' is not supported.");
		}
	}
}