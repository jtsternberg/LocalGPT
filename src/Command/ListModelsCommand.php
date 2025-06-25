<?php

namespace LocalGPT\Command;

use LocalGPT\Exceptions;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'models',
	description: 'Lists all available models from supported AI providers.'
)]
class ListModelsCommand extends Command
{
	public function __construct(protected ?ProviderFactory $providerFactory = null, protected ?ConfigService $configService = null)
	{
		parent::__construct();
		$this->configService   = $this->configService ?? new ConfigService();
		$this->providerFactory = $this->providerFactory ?? new ProviderFactory($this->configService);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$io->title('Available Models');

		foreach (array_keys(ProviderFactory::SUPPORTED_PROVIDERS) as $providerName) {
			try {
				$providerInstance = $this->providerFactory->createProviderByName($providerName);
				$models           = $providerInstance->listModels();

				if (empty($models)) {
					$io->warning("No models found for the {$providerName} provider.");
					continue;
				}

				$io->section(ucfirst($providerName) . ' Models');
				$io->listing($models);
			} catch (Exceptions\MissingProviderApiKeyException $e) {
				// Ignore these.
			} catch (\Exception $e) {
				$io->warning(ucfirst($providerName) . ': ' . $e->getMessage());
			}
		}

		return Command::SUCCESS;
	}
}