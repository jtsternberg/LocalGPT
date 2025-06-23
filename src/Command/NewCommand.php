<?php

namespace LocalGPT\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use LocalGPT\Service\ProviderFactory;
use LocalGPT\Service\Config;

#[AsCommand(
	name: 'new',
	description: 'Creates a new GPT configuration.'
)]
class NewCommand extends Command
{
	protected function configure()
	{
		$this->addArgument('name', InputArgument::REQUIRED, 'The name of the GPT to create.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$name = $input->getArgument('name');
		$configService = new Config();
		$providerFactory = new ProviderFactory($configService);

		$io->title('Create a new GPT: ' . $name);

		$title = $io->ask('Enter the title for the GPT');
		$description = $io->ask('Enter the description');
		$providerName = $io->choice(
			'Select a provider',
			ProviderFactory::SUPPORTED_PROVIDERS,
			'gemini'
		);

		$model = '';
		try {
			$provider = $providerFactory->createProvider($providerName);
			$models = $provider->listModels();
			if (!empty($models)) {
				$defaultModel = $provider->getDefaultModel();
				$defaultModel = in_array($defaultModel, $models) ? $defaultModel : $models[0];
				$model = $io->choice('Select a model', $models, $defaultModel);
			} else {
				$io->warning("No models found for the {$providerName} provider. Please enter one manually.");
				$model = $io->ask("Enter the model for {$providerName}");
			}
		} catch (\Exception $e) {
			$io->warning("Could not fetch models for the {$providerName} provider. You can set the model manually.");
			$io->warning($e->getMessage());
			$model = $io->ask("Enter the model for {$providerName}");
		}

		if (empty($model)) {
			$io->error('Model name cannot be empty.');
			return Command::FAILURE;
		}

		$systemPrompt = $io->ask('Enter the system prompt');

		$gptDir = getcwd() . '/' . $name;
		if (!is_dir($gptDir)) {
			mkdir($gptDir, 0777, true);
		}

		$refDir = $gptDir . '/reference-files';
		if (!is_dir($refDir)) {
			mkdir($refDir, 0777, true);
		}

		file_put_contents($gptDir . '/SYSTEM_PROMPT.md', $systemPrompt);

		$config = [
			'provider' => $providerName,
			'title' => $title,
			'description' => $description,
			'model' => $model,
			'system_prompt' => './SYSTEM_PROMPT.md',
			'reference_files' => [],
		];

		file_put_contents($gptDir . '/gpt.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

		$io->success("GPT '{$name}' created successfully.");

		return Command::SUCCESS;
	}
}