<?php

namespace jonpugh\ComposerGitBuild;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Command\BaseCommand;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return array(new Command);
    }
}
