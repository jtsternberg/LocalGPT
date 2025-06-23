<?php

namespace LocalGPT\Provider;

abstract class BaseProvider implements ProviderInterface
{
	protected $client;
	protected $model;
	protected $systemPrompt;

	public abstract function __construct(string $apiKey);
	public abstract function listModels(): array;
	public abstract function chat(array $messages): string;

	public function setModel(string $model): void
	{
		$this->model = $model;
	}

	public function getDefaultModel(): string
	{
		return static::DEFAULT_MODEL;
	}

	public function setSystemPrompt(string $systemPrompt): void
	{
		$this->systemPrompt = $systemPrompt;
	}

}