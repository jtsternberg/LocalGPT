<?php

namespace LocalGPT\Command;

use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Service\ProviderFactory;
use LocalGPT\Provider\ProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'chat',
	description: 'Starts a chat with a GPT.'
)]
class ChatCommand extends Command
{
	use VerboseCommandTrait;

	protected GptConfig $config;
	protected ProviderInterface $provider;
	protected SymfonyStyle $io;
	protected InputInterface $input;

	public function __construct(
		protected ?ConfigService $configService = null,
		protected ?ProviderFactory $providerFactory = null,
		bool $verbose = false
	) {
		parent::__construct();
		$this->verbose         = $verbose;
		$this->configService   = $this->configService ?? new ConfigService();
		$this->providerFactory = $this->providerFactory ?? new ProviderFactory($this->configService);
	}

	protected function configure()
	{
		$this->addArgument('name', InputArgument::REQUIRED, 'The name of the GPT to chat with.');
		$this->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Send a single message and exit.');
		$this->addOption('messageFile', 'f', InputOption::VALUE_REQUIRED, 'Send the contents of a file as a single message and exit.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->io = new SymfonyStyle($input, $output);
		$this->setVerbose($input);

		$name = $input->getArgument('name');

		try {
			$this->config   = new GptConfig($this->configService->loadGptConfig($name));
			$this->provider = $this->providerFactory->createProvider($this->config);

			if ($message = $input->getOption('message')) {
				$this->handleSingleMessage($message);
			} elseif ($messageFile = $input->getOption('messageFile')) {
				$this->handleMessageFile($messageFile);
			} else {
				$this->handleInteractiveChat();
			}
		} catch (\Exception $e) {
			$this->io->error($e->getMessage());
			return Command::FAILURE;
		}

		return Command::SUCCESS;
	}

	protected function handleSingleMessage(string $message): void
	{
		$messages[] = ['role' => 'user', 'parts' => [['text' => $message]]];
		$response = $this->provider->chat($messages);

		try {
			if ($this->verbose) {
				$this->printDetails(false);
			}
		} catch (\Exception $e) {
		}

		$this->io->writeln($response);
	}

	protected function handleMessageFile(string $filePath): void
	{
		if (!file_exists($filePath)) {
			throw new \InvalidArgumentException("File not found: {$filePath}");
		}

		$message = file_get_contents($filePath);
		$this->handleSingleMessage($message);
	}

	protected function handleInteractiveChat(): void
	{
		$this->printDetails();

		$this->io->writeln('You can start chatting now. (type \'exit\' or Ctrl+C to quit)');
		while (true) {
			$userInput = $this->io->ask('> ');

			if ($userInput === null || strtolower($userInput) === 'exit') {
				break;
			}

			$messages[] = ['role' => 'user', 'parts' => [['text' => $userInput]]];
			$response = $this->provider->chat($messages);
			$messages[] = ['role' => 'model', 'parts' => [['text' => $response]]];

			$this->io->writeln('');
			$this->io->writeln('🤖 ' . $response);
		}

		$this->io->writeln('Chat session ended.');
	}

	public function printDetails( $interactive = true ) {
		$title = $this->config->get('title');
		$description = $this->config->get('description');
		if ($description) {
			$title = $title . ' - ' . $description;
		}

		$verb = $interactive ? 'Loading' : 'Using';
		$this->io->writeln($verb . ' GPT: ' . $title);
		$this->io->writeln('Provider: ' . $this->config->get('provider'));
		$this->io->writeln('Model: ' . $this->config->get('model'));
		$this->io->writeln('---');
		$this->io->newLine();
	}

}