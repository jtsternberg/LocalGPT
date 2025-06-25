<?php

namespace LocalGPT\Command;

use Symfony\Component\Console\Input\InputInterface;

trait VerboseCommandTrait
{
	protected bool $verbose = false;

	// Verbose seems to already be registered by Symfony, so we don't need to add it here.
	// protected function configure()
	// {
	// 	$this->addOption('verbose', 'v', InputOption::VALUE_NONE, 'Verbose output.');
	// }

	protected function setVerbose(InputInterface $input): self
	{
		try {
			if ($input->getOption('verbose') || $input->getOption('v')) {
				$this->verbose = true;
			}
		} catch (\Exception $e) {
		}

		return $this;
	}

}