<?php

namespace LocalGPT\Tests\Service;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use LocalGPT\Provider\GeminiProvider;
use LocalGPT\Provider\OllamaProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProviderFactoryTest extends TestCase
{
    private MockObject|ConfigService $configServiceMock;
    private ProviderFactory $providerFactory;

    protected function setUp(): void
    {
        $this->configServiceMock = $this->createMock(ConfigService::class);
        $this->providerFactory = new ProviderFactory($this->configServiceMock);
    }

    public function testGetProviderClass()
    {
        $this->assertEquals(GeminiProvider::class, $this->providerFactory->getProviderClass('gemini'));
    }

    public function testGetProviderClassThrowsExceptionForUnsupportedProvider()
    {
        $this->expectException(\Exception::class);
        $this->providerFactory->getProviderClass('unsupported-provider');
    }

    public function testGetProviderApiKey()
    {
        $this->configServiceMock->method('getApiKey')->with('gemini')->willReturn('test-api-key');
        $apiKey = $this->providerFactory->getProviderApiKey('gemini');
        $this->assertEquals('test-api-key', $apiKey);
    }

    public function testGetProviderApiKeyForOllama()
    {
        $apiKey = $this->providerFactory->getProviderApiKey('ollama');
        $this->assertEquals('ollama', $apiKey);
    }

    public function testGetProviderApiKeyThrowsExceptionForMissingKey()
    {
        $this->configServiceMock->method('getApiKey')->with('gemini')->willReturn(null);
        $this->expectException(\Exception::class);
        $this->providerFactory->getProviderApiKey('gemini');
    }

    public function testCreateProvider()
    {
        // Mock GptConfig
        $gptConfigMock = $this->createMock(GptConfig::class);
        $gptConfigMock->method('get')->willReturnMap([
            ['provider', 'gemini'],
            ['model', 'test-model'],
            ['system_prompt', ''],
            ['reference_files', []],
        ]);

        // Mock ConfigService dependency for API key
        $this->configServiceMock->method('getApiKey')->with('gemini')->willReturn('test-api-key');

        $provider = $this->providerFactory->createProvider($gptConfigMock);

        $this->assertInstanceOf(GeminiProvider::class, $provider);
    }

    public function testCreateProviderForOllama()
    {
        // Mock GptConfig
        $gptConfigMock = $this->createMock(GptConfig::class);
        $gptConfigMock->method('get')->willReturnMap([
            ['provider', 'ollama'],
            ['model', 'test-model'],
            ['system_prompt', ''],
            ['reference_files', []],
        ]);

        $provider = $this->providerFactory->createProvider($gptConfigMock);

        $this->assertInstanceOf(OllamaProvider::class, $provider);
    }
}