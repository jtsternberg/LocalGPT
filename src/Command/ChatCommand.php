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
	protected function configure()
	{
		$this->addArgument('name', InputArgument::REQUIRED, 'The name of the GPT to chat with.');
		$this->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Send a single message and exit.');
		$this->addOption('messageFile', 'f', InputOption::VALUE_REQUIRED, 'Send the contents of a file as a single message and exit.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$name = $input->getArgument('name');
		$configService = new ConfigService();
		$providerFactory = new ProviderFactory($configService);

		try {
			$config = new GptConfig($configService->loadGptConfig($name));
			$provider = $providerFactory->createProvider($config);
		} catch (\Exception $e) {
			$io->error($e->getMessage());
			return Command::FAILURE;
		}

		$message = $input->getOption('message');
		$messageFile = $input->getOption('messageFile');

		if (!empty($messageFile)) {
			if (file_exists($messageFile)) {
				$message = file_get_contents($messageFile);
			} else {
				$io->error('The specified message file does not exist: ' . $messageFile);
				return Command::FAILURE;
			}
		}

		if (!empty($message)) {
			$messages[] = ['role' => 'user', 'parts' => [['text' => $message]]];
			$response = $provider->chat($messages);
			$output->writeln($response);
			return Command::SUCCESS;
		}

		$io->writeln('Loading GPT: ' . $config->get('title'));
		$io->writeln('Provider: ' . $config->get('provider'));
		$io->writeln('Model: ' . $config->get('model'));
		$io->newLine();

		$io->writeln('You can start chatting now. (type \'exit\' to quit)');
		$io->newLine();

		$messages = [];
		while (true) {
			$userInput = $io->ask('> ');

			if ($userInput === null || strtolower($userInput) === 'exit') {
				break;
			}

			$messages[] = ['role' => 'user', 'parts' => [['text' => $userInput]]];
			$response = $provider->chat($messages);
			$messages[] = ['role' => 'model', 'parts' => [['text' => $response]]];

			$io->writeln('🤖 ' . $response);
		}

		$io->writeln('Chat session ended.');
		return Command::SUCCESS;
	}
}