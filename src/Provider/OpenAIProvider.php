<?php

namespace LocalGPT\Provider;

use LLPhant\Chat\OpenAIChat;
use LLPhant\OpenAIConfig;

class OpenAIProvider extends BaseProvider
{
	public const DEFAULT_MODEL = 'gpt-4o-mini';

	public function __construct(string $apiKey)
	{
		parent::__construct($apiKey);

		$config = new OpenAIConfig();
		$config->apiKey = $apiKey;
		$config->model = self::DEFAULT_MODEL;
		$this->client = new OpenAIChat($config);
	}

	public function setModel(string $model): void
	{
		parent::setModel($model);
		$this->client->model = $model;
	}

	public function listModels(): array
	{
		return [
			'gpt-4o',
			'gpt-4o-mini',
			'gpt-4-turbo',
			'gpt-3.5-turbo',
		];
	}
}