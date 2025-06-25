<?php

namespace LocalGPT\Tests\Provider;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Provider\GeminiProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class GeminiProviderTest extends TestCase
{
	private MockObject|GptConfig $configMock;
	private GeminiProvider $provider;

	protected function setUp(): void
	{
		$this->configMock = $this->createMock(GptConfig::class);
		$this->provider = new GeminiProvider('test-api-key');
		$this->provider->setConfig($this->configMock);
	}

	public function testPlaceholder()
	{
		// Update tests once [PR #264](https://github.com/LLPhant/LLPhant/pull/264) is merged and released.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}