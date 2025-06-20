<?php

namespace LocalGPT\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

        $io->title('Create a new GPT: ' . $name);

        $title = $io->ask('Enter the title for the GPT');
        $description = $io->ask('Enter the description');
        $provider = $io->ask('Enter the provider (default: gemini): ', 'gemini');
        $model = $io->ask('Enter the model (default: gemini-1.5-flash): ', 'gemini-1.5-flash');
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
            'provider' => $provider,
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