<?php

namespace LocalGPT\Service;

use Dotenv\Dotenv;

class Config
{
    protected $basePath;
    protected $env;

    public function __construct()
    {
        $this->basePath = getcwd();
        $this->env = Dotenv::createImmutable($this->basePath)->load();
    }

    public function loadGptConfig(string $gptName): array
    {
        $configPath = $this->basePath . '/' . $gptName . '/gpt.json';

        if (!file_exists($configPath)) {
            throw new \InvalidArgumentException("Configuration file not found for GPT: {$gptName}");
        }

        $configJson = file_get_contents($configPath);
        $config = json_decode($configJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Error parsing JSON from {$configPath}: " . json_last_error_msg());
        }

        return $config;
    }

    public function getApiKey(string $provider): ?string
    {
        $keyName = strtoupper($provider) . '_API_KEY';
        return $this->env[$keyName] ?? null;
    }
}