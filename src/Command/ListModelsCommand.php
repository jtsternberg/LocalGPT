<?php

namespace LocalGPT\Command;

use LocalGPT\Exceptions;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'models',
	description: 'Lists all available models from supported AI providers.'
)]
class ListModelsCommand extends Command
{
	use VerboseCommandTrait;

	public function __construct(
		protected ?ProviderFactory $providerFactory = null,
		protected ?ConfigService $configService = null,
		bool $verbose = false
	)
	{
		parent::__construct();
		$this->verbose         = $verbose;
		$this->configService   = $this->configService ?? new ConfigService();
		$this->providerFactory = $this->providerFactory ?? new ProviderFactory($this->configService);
	}

	protected function configure()
	{
		$this->addArgument('modelId', InputArgument::OPTIONAL, 'Show details for a specific model ID.');
		$this->addOption('provider', 'p', InputOption::VALUE_REQUIRED, 'Filter models by provider.');
		// Verbose seems to already be registered by Symfony, so we don't need to add it here.
		// $this->addOption('verbose', 'v', InputOption::VALUE_NONE, 'Verbose output.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$this->setVerbose($input);

		$modelId = $input->getArgument('modelId');

		if ($modelId) {
			// TODO: Implement model details display.
			// See ROADMAP.md for more details.
			$io->writeln("Showing details for model: {$modelId}");

			return Command::SUCCESS;
		}


		$providerFilter = $input->getOption('provider');
		foreach (array_keys(ProviderFactory::SUPPORTED_PROVIDERS) as $providerName) {
			if ($providerFilter && $providerName !== $providerFilter) {
				continue;
			}

			try {
				$providerInstance = $this->providerFactory->createProviderByName($providerName);
				$models           = $providerInstance->listModels();

				if (empty($models)) {
					$io->warning("No models found for the {$providerName} provider.");
					continue;
				}

				$io->section(ucfirst($providerName) . ' Models');
				if ($this->verbose) {
					foreach ($models as $model) {
						// TODO: Display model details.
						// See ROADMAP.md for more details.
					}
				} else {
					$io->listing($models);
				}
			} catch (Exceptions\MissingProviderApiKeyException $e) {
				// Ignore these.
			} catch (\Exception $e) {
				$io->warning(ucfirst($providerName) . ': ' . $e->getMessage());
			}
		}

		return Command::SUCCESS;
	}
}