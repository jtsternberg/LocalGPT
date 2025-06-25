<?php

namespace LocalGPT\Provider;

use LLPhant\Chat\AnthropicChat;
use LLPhant\AnthropicConfig;

class AnthropicProvider extends BaseProvider
{
	public const DEFAULT_MODEL = 'claude-3-5-sonnet-20240620';
	protected $name = 'anthropic';

	public function __construct(string $apiKey, ?AnthropicChat $client = null)
	{
		parent::__construct($apiKey);

		if ($client) {
			$this->client = $client;
			return;
		}

		$config = new AnthropicConfig(
			model: self::DEFAULT_MODEL,
			apiKey: $this->apiKey
		);
		$this->client = new AnthropicChat($config);
	}

	public function setModel(string $model): void
	{
		parent::setModel($model);
		$config = new AnthropicConfig(
			model: $model,
			apiKey: $this->apiKey
		);
		$this->client = new AnthropicChat($config);
	}

	public function listModels(): array
	{
		return [
			'claude-3-haiku-20240307',
			'claude-3-5-sonnet-20240620',
			'claude-3-5-sonnet-20241022',
			'claude-3-sonnet-20240229',
			'claude-3-opus-20240229',
		];
	}
}