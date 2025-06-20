<?php

namespace LocalGPT\Service;

class Config
{
    protected $basePath;

    public function __construct()
    {
        $this->basePath = getcwd();
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
}