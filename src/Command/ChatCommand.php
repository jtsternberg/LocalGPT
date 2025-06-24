<?php

namespace LocalGPT\Command;

use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Service\ProviderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'chat',
	description: 'Starts a chat session with a specified GPT.'
)]
class ChatCommand extends Command
{
	protected SymfonyStyle $io;

	protected function configure()
	{
		$this->addArgument('name', InputArgument::REQUIRED, 'The name of the GPT to chat with.');
		$this->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Send a single message and exit.');
		$this->addOption('messageFile', 'f', InputOption::VALUE_REQUIRED, 'Send the contents of a file as a single message and exit.');
		// Verbose seems to already be registered by Symfony, so we don't need to add it here.
		// $this->addOption('verbose', 'v', InputOption::VALUE_NONE, 'Verbose output.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->io = new SymfonyStyle($input, $output);
		$name = $input->getArgument('name');
		$configService = new ConfigService();
		$providerFactory = new ProviderFactory($configService);

		try {
			$config = new GptConfig($configService->loadGptConfig($name));
			$provider = $providerFactory->createProvider($config);
		} catch (\Exception $e) {
			$this->io->error($e->getMessage());
			return Command::FAILURE;
		}

		$message = $input->getOption('message');
		$messageFile = $input->getOption('messageFile');

		if (!empty($messageFile)) {
			if (file_exists($messageFile)) {
				$message = file_get_contents($messageFile);
			} else {
				$this->io->error('The specified message file does not exist: ' . $messageFile);
				return Command::FAILURE;
			}
		}

		if (!empty($message)) {
			$messages[] = ['role' => 'user', 'parts' => [['text' => $message]]];
			$response = $provider->chat($messages);

			if ($input->getOption('verbose') || $input->getOption('v')) {
				$this->printDetails($config, false);
			}

			$output->writeln($response);
			return Command::SUCCESS;
		}

		$this->printDetails($config);

		$this->io->writeln('You can start chatting now. (type \'exit\' or Ctrl+C to quit)');
		$this->io->newLine();

		$messages = [];
		while (true) {
			$userInput = $this->io->ask('> ');

			if ($userInput === null || strtolower($userInput) === 'exit') {
				break;
			}

			$messages[] = ['role' => 'user', 'parts' => [['text' => $userInput]]];
			$response = $provider->chat($messages);
			$messages[] = ['role' => 'model', 'parts' => [['text' => $response]]];

			$this->io->writeln('🤖 ' . $response);
		}

		$this->io->writeln('Chat session ended.');
		return Command::SUCCESS;
	}

	public function printDetails( GptConfig $config, $interactive = true ) {
		$title = $config->get('title');
		$description = $config->get('description');
		if ($description) {
			$title = $title . ' - ' . $description;
		}

		$verb = $interactive ? 'Loading' : 'Using';
		$this->io->writeln($verb . ' GPT: ' . $title);
		$this->io->writeln('Provider: ' . $config->get('provider'));
		$this->io->writeln('Model: ' . $config->get('model'));
		$this->io->writeln('---');
		$this->io->newLine();
	}
}