<?php

namespace LocalGPT\Provider;

interface ProviderInterface
{
    /**
     * @param array $messages The array of messages for the chat context.
     * @return string The response from the provider.
     */
    public function chat(array $messages): string;
}