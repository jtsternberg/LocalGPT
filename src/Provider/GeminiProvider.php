<?php

namespace LocalGPT\Provider;

use Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;

class GeminiProvider implements ProviderInterface
{
	protected $client;
	protected $model;
	public const DEFAULT_MODEL = 'gemini-2.5-flash';

	public function __construct(string $apiKey)
	{
		$this->client = Gemini::client($apiKey);
	}

	public function setModel(string $model): void
	{
		$this->model = $model;
	}

	public function listModels(): array
	{
		$response = $this->client->models()->list();

		$models = [];
		foreach ($response->models as $model) {
			if (
				in_array('generateContent', $model->supportedGenerationMethods, true) &&
				! str_contains($model->name, 'vision')
			) {
				$models[] = str_replace('models/', '', $model->name);
			}
		}

		return $models;
	}

	public function chat(array $messages): string
	{
		$history = [];

		// The last message is the new prompt, so we remove it from the history
		// and send it in the `sendMessage` call.
		$lastMessage = array_pop($messages);

		if (empty($lastMessage['parts'][0]['text'])) {
			return '';
		}

		foreach ($messages as $message) {
			$history[] = Content::parse(
				part: $message['parts'][0]['text'],
				role: Role::from($message['role'])
			);
		}

		$chat = $this->client
			->generativeModel($this->model)
			->startChat($history);

		$response = $chat->sendMessage($lastMessage['parts'][0]['text']);

		return $response->text();
	}

	public function getDefaultModel(): string
	{
		return self::DEFAULT_MODEL;
	}
}