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
            ScriptEvents::POST_INSTALL_CMD => 'postCmd',
        );
    }
    
    /**
     * Post command event callback.
     *
     * @param \Composer\Script\Event $event
     */
    public function postCmd(\Composer\Script\Event $event) {
        $this->io->writeln(['POST_INSTALL_CMD => postCmd']);
    }
    
    
    function build() {
        $this->io-writeln(['Hi there...']);
    }
}