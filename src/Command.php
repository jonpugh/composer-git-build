<?php

namespace jonpugh\ComposerGitBuild;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

/**
 * Class Command
 *
 * Provides the `git-build` command to composer.
 *
 * @package jonpugh\ComposerGitBuild
 */
class Command extends BaseCommand
{
    protected function configure()
    {
        $this->setName('git-build');
        $this->setDescription('Add all vendor code and ignored dependencies to git.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Adding vendor code to git...');
        $output->writeln('<comment>COMING SOON!</comment>');
    }
}