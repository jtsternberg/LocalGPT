<?php

namespace LocalGPT\Command;

use LocalGPT\Service\Config;
use LocalGPT\Service\ProviderFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$name = $input->getArgument('name');
		$configService = new Config();
		$providerFactory = new ProviderFactory($configService);

		try {
			$gptConfig = $configService->loadGptConfig($name);
			$provider = $providerFactory->createProvider($gptConfig['provider']);
			$provider->setModel($gptConfig['model']);
		} catch (\Exception $e) {
			$io->error($e->getMessage());
			return Command::FAILURE;
		}

		$io->writeln('Loading GPT: ' . $gptConfig['title']);
		$io->writeln('Provider: ' . $gptConfig['provider']);
		$io->writeln('Model: ' . $gptConfig['model']);
		$io->newLine();

		$gptDir = getcwd() . '/' . $name;
		$systemPrompt = file_get_contents($gptDir . '/' . ltrim($gptConfig['system_prompt'], './'));

		$provider->setSystemPrompt($systemPrompt);

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