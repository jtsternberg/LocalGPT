<?php

namespace LocalGPT\Provider;

use Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use LocalGPT\Service\Config;

class GeminiProvider implements ProviderInterface
{
    protected $client;
    protected $model;

    public function __construct(string $apiKey, string $model = 'gemini-1.5-flash')
    {
        $this->client = Gemini::client($apiKey);
        $this->model = $model;
    }

    public function chat(array $messages): string
    {
        $history = [];

        // The last message is the new prompt, so we remove it from the history
        // and send it in the `sendMessage` call.
        $lastMessage = array_pop($messages);

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
}