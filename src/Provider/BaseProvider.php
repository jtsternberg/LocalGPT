<?php

namespace LocalGPT\Provider;

use LocalGPT\Models\Config;

abstract class BaseProvider implements ProviderInterface
{
	public const DEFAULT_MODEL = '';
	protected $client;
	protected string $apiKey;
	protected $model;
	protected $systemPrompt;
	protected array $referenceFiles = [];

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

	public function setConfig(Config $config): void
	{
		if ($model = $config->get('model')) {
			$this->setModel($model);
		}

		if ($systemPrompt = $config->getSystemPromptText()) {
			$this->setSystemPrompt($systemPrompt);
		}

		if ($referenceFiles = $config->getReferenceFilesWithContent()) {
			$this->referenceFiles = $referenceFiles;
		}
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

		$systemMessage = $this->buildSystemPrompt();

		if (!empty($systemMessage)) {
			$this->client->setSystemMessage($systemMessage);
		}

		// For now, we will not send the history to the LLM.
		// We will implement this in a future step.

		return $this->client->generateText($lastMessage['parts'][0]['text']);
	}

	protected function buildSystemPrompt(): string
	{
		$systemMessage = $this->systemPrompt ?? '';
		if (!empty($this->referenceFiles)) {
			$systemMessage .= "\n\n--- REFERENCE MATERIALS ---\n";
			$systemMessage .= "You have been provided with the following reference files. Use them to inform your responses.\n\n";
			foreach ($this->referenceFiles as $file) {
				$systemMessage .= "### " . basename($file['path']) . " ###\n";
				$systemMessage .= $file['content'] . "\n\n";
			}
			$systemMessage .= "\n\n--- END REFERENCE MATERIALS ---\n";
		}

		if (!empty( $systemMessage )) {
			$systemMessage = "--- SYSTEM PROMPT ---\n" . $systemMessage . "\n\n--- END SYSTEM PROMPT ---\n\n";
		}

		return $systemMessage;
	}
}