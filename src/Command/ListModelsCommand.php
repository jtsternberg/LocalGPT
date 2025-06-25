<?php

namespace LocalGPT\Command;

use LocalGPT\Provider\ProviderInterface;
use LocalGPT\Exceptions;
use LocalGPT\Provider\OllamaProvider;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use LocalGPT\Service\ModelsDevService;
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

	protected SymfonyStyle $io;

	public function __construct(
		protected ?ProviderFactory $providerFactory = null,
		protected ?ConfigService $configService = null,
		protected ?ModelsDevService $modelsDevService = null,
		bool $verbose = false
	)
	{
		parent::__construct();
		$this->verbose         = $verbose;
		$this->configService   = $this->configService ?? new ConfigService();
		$this->providerFactory = $this->providerFactory ?? new ProviderFactory($this->configService);
		$this->modelsDevService = $this->modelsDevService ?? new ModelsDevService();
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
		$this->setVerbose($input);
		$this->io = new SymfonyStyle($input, $output);

		$modelId = $input->getArgument('modelId');

		if ($modelId) {
			return $this->outputModelDetails($modelId);
		}
		$this->io->title('Available Models');

		$providerFilter = $input->getOption('provider');
		foreach (array_keys(ProviderFactory::SUPPORTED_PROVIDERS) as $providerName) {
			if ($providerFilter && $providerName !== $providerFilter) {
				continue;
			}

			try {
				$providerInstance = $this->providerFactory->createProviderByName($providerName);
				$models           = $providerInstance->listModels();

				if (empty($models)) {
					$this->io->warning("No models found for the {$providerName} provider.");
					continue;
				}

				$this->io->section(ucfirst($providerName) . ' Models');

				if ($this->verbose) {
					$this->verboseModelDetails($models, $providerInstance);
				} else {
					$this->io->listing($models);
				}
			} catch (Exceptions\MissingProviderApiKeyException $e) {
				// Ignore these.
			} catch (\Exception $e) {
				$this->io->warning(ucfirst($providerName) . ': ' . $e->getMessage());
			}
		}

		return Command::SUCCESS;
	}

	protected function verboseModelDetails(array $models, ProviderInterface $providerInstance): int
	{
		$fails = [];
		foreach ($models as $model) {
			$result = $providerInstance instanceof OllamaProvider
				? $this->outputOllamaModelDetails($providerInstance, $model)
				: $this->showModelDetails($model);

			if ( Command::INVALID === $result ) {
				$fails[] = $model;
			}
		}

		if (!empty($fails)) {
			$this->io->error('Could not find information (from models.dev) for the following ' . ucfirst($providerInstance->getName()) . ' models:');
			$this->io->listing($fails);

			return Command::INVALID;
		}

		return Command::SUCCESS;
	}

	protected function outputModelDetails(string $modelId): int
	{
		$result = $this->showModelDetails($modelId);
		if ( Command::INVALID === $result ) {

			// Check if model is ollama.
			$ollama = $this->providerFactory->createProviderByName('ollama');
			$models = $ollama->listModels();
			if ( in_array($modelId, $models, true) ) {
				$result = $this->outputOllamaModelDetails($ollama, $modelId);
			}
		}

		if ( Command::INVALID === $result ) {
			$this->io->error("Information for model {$modelId} not found (on models.dev).");
		}

		return $result;
	}

	protected function showModelDetails(string $modelId): int
	{
		[$model, $providerId] = $this->modelsDevService->findModel($modelId);

		if (!$model) {
			return Command::INVALID;
		}

		$this->io->title("Model Details: {$model['id']}");
		$this->io->writeln("Name: <info>{$model['name']}</info>");
		$this->io->writeln("Provider: <info>{$providerId}</info>");

		if (isset($model['limit']['context'])) {
			$this->io->writeln("Context Window: <info>{$model['limit']['context']}</info>");
		}

		if (isset($model['cost']['input'])) {
			$this->io->writeln("Input Cost: <info>\${$model['cost']['input']} / 1M tokens</info>");
		}
		if (isset($model['cost']['output'])) {
			$this->io->writeln("Output Cost: <info>\${$model['cost']['output']} / 1M tokens</info>");
		}
		if (isset($model['cost']['cache_read'])) {
			$this->io->writeln("Cache Read Cost: <info>\${$model['cost']['cache_read']} / 1M tokens</info>");
		}

		if (isset($model['modalities']['input'])) {
			$this->io->writeln("Input Modalities: <info>" . implode(', ', $model['modalities']['input']) . "</info>");
		}
		if (isset($model['modalities']['output'])) {
			$this->io->writeln("Output Modalities: <info>" . implode(', ', $model['modalities']['output']) . "</info>");
		}

		if (isset($model['knowledge'])) {
			$this->io->writeln("Knowledge Cutoff: <info>{$model['knowledge']}</info>");
		}
		if (isset($model['release_date'])) {
			$this->io->writeln("Release Date: <info>{$model['release_date']}</info>");
		}
		if (isset($model['last_updated'])) {
			$this->io->writeln("Last Updated: <info>{$model['last_updated']}</info>");
		}
		if (isset($model['modalities']['input'])) {
			$this->io->writeln("Input Modalities: <info>" . implode(', ', $model['modalities']['input']) . "</info>");
		}

		$features = [];
		foreach ([
			'attachment' => 'Attachments',
			'reasoning' => 'Reasoning',
			'temperature' => 'Temperature Control',
			'tool_call' => 'Tool Calling',
		] as $key => $value) {
			if (!empty($model[$key])) {
				$features[] = $value;
			}
		}

		if (!empty($features)) {
			$this->io->writeln("Features: <info>" . implode(', ', $features) . "</info>");
		}

		return Command::SUCCESS;
	}

	public function outputOllamaModelDetails( OllamaProvider $ollama, string $modelId ): int
	{
		$details = $ollama->getModelDetails($modelId);
		if ( empty($details) ) {
			return Command::INVALID;
		}

		$this->io->title("Model Details: {$modelId}");

		$this->io->writeln($details);

		return Command::SUCCESS;
	}
}