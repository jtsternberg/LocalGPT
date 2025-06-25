<?php

namespace LocalGPT\Provider;

use LocalGPT\Models\Config;

interface ProviderInterface
{
	public function setConfig(Config $config): void;
	public function setModel(string $model): void;
	public function getName(): string;
	public function listModels(): array;
	public function chat(array $history): string;
	public function getDefaultModel(): string;
	public function setSystemPrompt(string $systemPrompt): void;
}