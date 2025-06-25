<?php

namespace LocalGPT\Command;

use LocalGPT\Service\Config as ConfigService;
use LocalGPT\Models\Config as GptConfig;
use LocalGPT\Service\Utils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'reference',
	description: 'Adds, removes, or lists reference files for a GPT.'
)]
class ReferenceCommand extends Command
{
	protected string $name;
	protected SymfonyStyle $io;
	protected GptConfig $config;

	public function __construct(protected ?ConfigService $configService = null)
	{
		parent::__construct();
		$this->configService = $this->configService ?? new ConfigService();
	}

	protected function configure()
	{
		$this->addArgument('name', InputArgument::REQUIRED, 'The name of the GPT to manage.');
		$this->addArgument('file-path', InputArgument::OPTIONAL, 'The path to the file to add.');
		$this->addOption('delete', 'd', InputOption::VALUE_REQUIRED, 'Remove a reference file by the file name.');
		$this->addOption('list', 'l', InputOption::VALUE_NONE, 'List all reference files.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->io = new SymfonyStyle($input, $output);

		try {
			$this->setupConfig($input->getArgument('name'));

			if ($input->getOption('list')) {
				$this->listReferenceFiles();
			} elseif ($filePathToDelete = $input->getOption('delete')) {
				$this->deleteReferenceFile($filePathToDelete);
			} elseif ($filePathToAdd = $input->getArgument('file-path')) {
				$this->addReferenceFile($filePathToAdd);
			} else {
				$this->io->writeln('Please provide a file to add, or use the --list or --delete options.');
			}

		} catch (\Exception $e) {
			$this->io->error($e->getMessage());
			return Command::FAILURE;
		}

		return Command::SUCCESS;
	}

	protected function deleteReferenceFile(string $filePathToDelete): void
	{
		$this->configService->deleteReferenceFile($this->config->name, $filePathToDelete);
		$this->io->success("Reference file removed: {$filePathToDelete}");

		// Refresh the config, and display the files list.
		$this->setupConfig()->listReferenceFiles();
	}

	protected function addReferenceFile(string $filePathToAdd): void
	{
		$newPath = $this->configService->saveReferenceFile($this->config->name, $filePathToAdd);
		if ($newPath) {
			$this->io->success("Reference file added: {$newPath}");

			// Refresh the config, and display the files list.
			$this->setupConfig()->listReferenceFiles();
		}
	}

	protected function listReferenceFiles(): void
	{
		$referenceFiles = $this->config->getReferenceFiles();

		if (empty($referenceFiles)) {
			$this->io->writeln('No reference files found.');
			return;
		}

		$path = $this->config->getPath();

		$referenceDir = $this->configService->getOrCreateReferenceDir($this->config->getName()) . '/';
		foreach ($referenceFiles as $key => $referenceFile) {
			$referenceFiles[$key] = str_replace($referenceDir, '', Utils::convertPathToAbsolute($referenceFile, $path));
		}

		$this->io->writeln('Reference files:');
		$this->io->listing($referenceFiles);
	}

	public function setupConfig($name = null): self
	{
		$this->config = new GptConfig($this->configService->loadGptConfig($name ?? $this->config->name));

		return $this;
	}
}