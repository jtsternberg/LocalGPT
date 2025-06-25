<?php

namespace LocalGPT\Exceptions;

class MissingProviderApiKeyException extends \Exception
{
	public function __construct(string $provider, int $code = 0, ?\Throwable $previous = null)
	{
		$message = "API key for {$provider} not found in .env file.";
		parent::__construct($message, $code, $previous);
	}
}