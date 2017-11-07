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
            InputOption::VALUE_NONE,
            'Allow committing even if git working copy is dirty (has modified files).'
        );
        $this->addOption(
            'dry-run',
            NULL,
            InputOption::VALUE_NONE,
            'Build and commit to the repository but do not push.'
        );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
        $this->io = new Style($input, $output);

        if (!$this->isGitMinimumVersionSatisfied()) {
            $this->io->error("Your git is out of date. Please update git to 2.0 or newer.");
            exit(1);
        }
    
        if ($input->getOption('dry-run')) {
            $this->io->warning("This will be a dry run, the artifact will not be pushed.");
        }
        $this->checkDirty($options);


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
    
    /**
     * Verifies that installed minimum git version is met.
     *
     * @param string $minimum_version
     *   The minimum git version that is required.
     *
     * @return bool
     *   TRUE if minimum version is satisfied.
     */
    public function isGitMinimumVersionSatisfied($minimum_version = '2.0') {
        if (version_compare($this->shell_exec("git --version | cut -d' ' -f3"), $minimum_version, '>=')) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Checks to see if current git branch has uncommitted changes.
     *
     * @throws \Exception
     *   Thrown if deploy.git.failOnDirty is TRUE and there are uncommitted
     *   changes.
     */
    protected function checkDirty($options) {
      exec('git status --porcelain', $result, $return);
      if (!$options['ignore-dirty'] && $return !== 0) {
        throw new \Exception("Unable to determine if local git repository is dirty.");
      }

      $dirty = (bool) $result;
      if ($dirty) {
        if ($options['ignore-dirty']) {
          $this->io->warning("There are uncommitted changes on the source repository.");
        }
        else {
          throw new \Exception("There are uncommitted changes, commit or stash these changes before running git-build.");
        }
      }
    }
}