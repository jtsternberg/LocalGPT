<?php

namespace LocalGPT\Command;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'models',
	description: 'Lists available models for a provider.'
)]
class ListModelsCommand extends Command
{
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$configService = new ConfigService();
		$providerFactory = new ProviderFactory($configService);

		foreach (ProviderFactory::SUPPORTED_PROVIDERS as $providerName => $class) {
			$io->section("Models for {$providerName}");

			try {
				$provider = $providerFactory->createProvider(new GptConfig([
					// Satisfy the constructor empty path check.
					'path' => 'path',
					'provider' => $providerName,
				]));
				$models = $provider->listModels();

				if (empty($models)) {
					$io->warning("No models found for the {$providerName} provider.");
					continue;
				}

				$io->listing($models);

			} catch (\Exception $e) {
				$io->error("An error occurred while fetching models for {$providerName}: " . $e->getMessage());
			}
		}

		return Command::SUCCESS;
	}
}