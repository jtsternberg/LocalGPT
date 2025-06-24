<?php

namespace LocalGPT\Provider;

abstract class BaseProvider implements ProviderInterface
{
	protected $client;
	protected string $apiKey;
	protected $model;
	protected $systemPrompt;

	public function __construct(string $apiKey)
	{
		$this->apiKey = $apiKey;
	}

	public abstract function listModels(): array;

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

	public function chat(array $messages): string
	{

		// The last message is the new prompt.
		$lastMessage = array_pop($messages);
		if (empty($lastMessage['parts'][0]['text'])) {
			return '';
		}

		if (!empty($this->systemPrompt)) {
			$this->client->setSystemMessage($this->systemPrompt);
		}

		// For now, we will not send the history to the LLM.
		// We will implement this in a future step.

		return $this->client->generateText($lastMessage['parts'][0]['text']);
	}
}