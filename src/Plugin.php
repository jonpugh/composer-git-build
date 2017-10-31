<?php

namespace jonpugh\ComposerGitBuild;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PreFileDownloadEvent;
use Composer\Script\ScriptEvents;


class Plugin implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;
    
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            'post-install-cmd' => 'postInstallCommand',
        );
    }
    
    /**
     * Post command event callback.
     *
     * @param \Composer\Script\Event $event
     */
    public function postInstallCommand(\Composer\Script\Event $event) {
        $this->io->writeln(['postInstallCommand']);
    }
    
    
    function build() {
        $this->io-writeln(['Hi there...']);
    }
}