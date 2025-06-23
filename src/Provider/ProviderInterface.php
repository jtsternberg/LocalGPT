<?php

namespace LocalGPT\Provider;

interface ProviderInterface
{
	public function setModel(string $model): void;
	public function listModels(): array;
	public function chat(array $history): string;
	public function getDefaultModel(): string;
}