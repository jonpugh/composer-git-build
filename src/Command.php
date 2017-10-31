<?php

namespace jonpugh\ComposerGitBuild;

use Symfony\Component\Console\Input\InputInterface;
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
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->workingDir = $this->getWorkingDir($input);
        $this->comment('Determining git toplevel from directory ' . $this->workingDir);

        $this->repoDir = $this->shell_exec('git rev-parse --show-toplevel', $this->workingDir);
    
        if (empty($this->repoDir)) {
            $this->io->error('No git repository found in composer project located at ' . $this->workingDir);
            exit(1);
        }
        else {
            $this->io->success('Found git working copy in folder: ' .  $this->repoDir);
        }
    
        $this->io->caution('Coming soon: adding code to git repo...');
    
    }
    
    /**
     * @param array|string $message
     * @param bool         $newLine
     */
    public function comment($message, $newLine = true)
    {
        $message = sprintf('<comment> %s</comment>', $message);
        if ($newLine) {
            $this->io->writeln($message);
        } else {
            $this->io->write($message);
        }
    }
    
    /**
     * Gets the default branch name for the deployment artifact.
     */
    protected function getDefaultBranchName() {
//        chdir($this->getConfigValue('repo.root'));
        $git_current_branch = $this->shell_exec("git rev-parse --abbrev-ref HEAD", $this->repoDir);
        $default_branch = $git_current_branch . '-build';
        return $default_branch;
    }
    
    /**
     * @param  InputInterface    $input
     * @throws \RuntimeException
     * @return string
     */
    private function getWorkingDir(InputInterface $input)
    {
        $workingDir = $input->getParameterOption(array('--working-dir', '-d'));
        if (false !== $workingDir && !is_dir($workingDir)) {
            throw new \RuntimeException('Invalid working directory specified, '.$workingDir.' does not exist.');
        }
        
        return $workingDir;
    }
    
    protected function shell_exec($cmd, $dir = '') {
        $oldWorkingDir = getcwd();
        chdir($dir? $dir: getcwd());
        $output = trim(shell_exec($cmd));
        chdir($oldWorkingDir);
        return $output;
    }
    
}