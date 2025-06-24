<?php

namespace LocalGPT\Provider;

use LLPhant\Chat\OllamaChat;
use LLPhant\OllamaConfig;

class OllamaProvider extends BaseProvider
{
	public const DEFAULT_MODEL = 'llama3:latest';
	private OllamaConfig $config;

	public function __construct(string $apiKey)
	{
		parent::__construct($apiKey);

		$this->config = new OllamaConfig();
		$this->config->model = self::DEFAULT_MODEL;
		// If you're not running Ollama on the default port, you might need to configure the client further.
		// e.g., $this->config->url = 'http://localhost:11435';
		$this->client = new OllamaChat($this->config);
	}

	public function setModel(string $model): void
	{
		parent::setModel($model);
		$this->config->model = $model;
		$this->client = new OllamaChat($this->config);
	}

	public function listModels(): array
	{
		// These are some popular models. Users can add any model they have pulled locally.
		$models = [
			'llama3',
			'llama2',
			'mistral',
			'codellama',
			'phi3',
			'gemma',
		];

		exec( 'ollama list', $output, $exitCode );
		if ($exitCode === 0) {
			// Parse results, as they look like:
			/*
			NAME               ID           SIZE   MODIFIED
			qwen2.5-coder:1.5b 6d3abb8d2d53 986 MB 4 months ago
			llama3:latest      365c0bd3c000 4.7 GB 4 months ago
			starcoder2:latest  9f4ae0aff61e 1.7 GB 4 months ago
			codegemma:latest   0c96700aaada 5.0 GB 4 months ago
			mistral:latest     f974a74358d6 4.1 GB 4 months ago
			*/
			$models = array_map(function($model) {
				if (empty($model)) {
					return '';
				}
				if (strpos($model, 'NAME') === 0) {
					return '';
				}

				//  Get the model name from the first column.
				return explode(' ', $model)[0];
			}, $output);
			$models = array_filter($models);
		} else {
			// TODO: output a warning, since it looks like ollama is not running.
		}

		return $models;
	}
}