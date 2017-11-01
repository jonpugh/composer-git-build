<?php

namespace jonpugh\ComposerGitBuild;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Command
 *
 * Provides the `git-build` command to composer.
 *
 * @package jonpugh\ComposerGitBuild
 */
class Command extends BaseCommand
{
    
    /**
     * @var SymfonyStyle
     */
    protected $io;
    
    /**
     * The directory containing composer.json.
     * @var String
     */
    protected $workingDir;
    
    /**
     * The directory containing the git repository.
     * @var String
     */
    protected $repoDir;
    
    protected function configure()
    {
        $this->setName('git-build');
        $this->setDescription('Add all vendor code and ignored dependencies to git.');
        
        $this->addOption(
            'branch',
            'b',
            InputOption::VALUE_REQUIRED,
            'Branch to create.'
        );
        $this->addOption(
            'tag',
            't',
            InputOption::VALUE_REQUIRED,
            'Tag to create.'
        );
        $this->addOption(
            'commit-msg',
            'm',
            InputOption::VALUE_REQUIRED,
            'Commit message to use.'
        );
        $this->addOption(
            'ignore-dirty',
            NULL,
            InputOption::VALUE_OPTIONAL,
            'Allow committing even if git working copy is dirty (has modified files).',
            FALSE
        );
        $this->addOption(
            'dry-run',
            NULL,
            InputOption::VALUE_OPTIONAL,
            'Build and commit to the repository but do not push.',
            FALSE
        );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new Style($input, $output);
        $this->workingDir = $this->getWorkingDir($input);

        $this->io->text('Determining git information for directory ' . $this->workingDir);

        // Get and Check Repo Directory.
        $this->repoDir = $this->shell_exec('git rev-parse --show-toplevel', $this->workingDir);
    
        if (empty($this->repoDir)) {
            $this->io->error('No git repository found in composer project located at ' . $this->workingDir);
            exit(1);
        }
        else {
            $this->io->comment('Found git working copy in folder: ' .  $this->repoDir);
        }
    
        // Get and Check Current git reference.
        if ($this->getCurrentBranchName()) {
            $this->io->comment('Found current git reference: ' .  $this->getCurrentBranchName());
        }
        else {
            $this->io->error('No git reference detected in ' . $this->workingDir);
            exit(1);
        }
    }
    
    /**
     * Gets the default branch name for the deployment artifact.
     */
    protected function getCurrentBranchName() {
        return $this->shell_exec("git rev-parse --abbrev-ref HEAD", $this->repoDir);
    }
    
    /**
     * Gets the default branch name for the deployment artifact.
     */
    protected function getDefaultBranchName() {
        $default_branch = $this->getCurrentBranchName() . '-build';
        return $default_branch;
    }
    
    /**
     * Just return the cwd. Composer automatically sets CWD to the working-dir option.
     *
     * @param  InputInterface    $input
     * @throws \RuntimeException
     * @return string
     */
    private function getWorkingDir(InputInterface $input)
    {
        return getcwd();
    }
    
    protected function shell_exec($cmd, $dir = '') {
        $oldWorkingDir = getcwd();
        chdir($dir? $dir: getcwd());
        $output = trim(shell_exec($cmd));
        chdir($oldWorkingDir);
        return $output;
    }
    
}