<?php

namespace LocalGPT\Command;

use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'new',
	description: 'Creates a new GPT configuration via an interactive wizard.'
)]
class NewCommand extends Command
{
	protected string $gptName;
	protected array $config = [];
	protected SymfonyStyle $io;

	public function __construct(
		protected ?ConfigService $configService = null,
		protected ?ProviderFactory $providerFactory = null
	) {
		parent::__construct();
		$this->configService   = $this->configService ?? new ConfigService();
		$this->providerFactory = $this->providerFactory ?? new ProviderFactory($this->configService);
	}

	protected function configure()
	{
		$this->addArgument('name', InputArgument::REQUIRED, 'The name for the new GPT config directory.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$name = $input->getArgument('name');
		$this->io = new SymfonyStyle($input, $output);
		$this->io->title('New GPT Configuration: ' . $name);
		$this->config = $this->configService->createGptConfig($name);

		try {
			$this->askQuestions();
			$systemPrompt = $this->io->ask('Enter the system prompt');
			if (empty($systemPrompt)) {
				throw new \Exception('System prompt cannot be empty.');
			}

			$this->configService->getOrCreateConfigDir($name);
			$this->configService->getOrCreateReferenceDir($name);

			$this->configService
				->saveSystemPrompt($name, $systemPrompt)
				->saveGptConfig($name, $this->config);

			$this->io->success('GPT configuration created successfully at ' . $this->config['path']);
		} catch (\Exception $e) {
			$this->io->error($e->getMessage());
			return Command::FAILURE;
		}

		return Command::SUCCESS;
	}

	protected function askQuestions(): void
	{
		$this->config['title']           = $this->io->ask('Title', 'My Awesome GPT');
		$this->config['description']     = $this->io->ask('Description', 'A helpful assistant.');
		$this->config['provider']        = $this->askProvider();
		$this->config['model']           = $this->askModel();
		$this->config['system_prompt']   = './SYSTEM_PROMPT.md';
		$this->config['reference_files'] = [];

		if (empty($this->config['model'])) {
			throw new \Exception('Model name cannot be empty.');
		}
	}

	protected function askProvider(): string
	{
		return $this->io->choice(
			'Select a provider',
			array_keys(ProviderFactory::SUPPORTED_PROVIDERS),
			'gemini'
		);
	}

	protected function askModel(): string
	{
		$providerName = $this->config['provider'];
		try {
			$provider = $this->providerFactory->createProviderByName($providerName);
			$models = $provider->listModels();
			if (!empty($models)) {
				$defaultModel = $provider->getDefaultModel();
				$defaultModel = in_array($defaultModel, $models) ? $defaultModel : $models[0];
				return $this->io->choice('Select a model', $models, $defaultModel);
			} else {
				$this->io->warning("No models found for the {$providerName} provider. Please enter one manually.");
			}
		} catch (\Exception $e) {
			$this->io->warning("Could not fetch models for the {$providerName} provider. You can set the model manually.");
			$this->io->warning($e->getMessage());
		}

		return $this->io->ask("Enter the model for {$providerName}");
	}
}