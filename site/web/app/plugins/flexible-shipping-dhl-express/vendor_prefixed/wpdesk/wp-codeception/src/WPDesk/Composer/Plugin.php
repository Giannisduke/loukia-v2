<?php

namespace DhlVendor\WPDesk\Composer\Codeception;

use DhlVendor\Composer\Composer;
use DhlVendor\Composer\IO\IOInterface;
use DhlVendor\Composer\Plugin\Capable;
use DhlVendor\Composer\Plugin\PluginInterface;
/**
 * Composer plugin.
 *
 * @package WPDesk\Composer\Codeception
 */
class Plugin implements \DhlVendor\Composer\Plugin\PluginInterface, \DhlVendor\Composer\Plugin\Capable
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;
    public function activate(\DhlVendor\Composer\Composer $composer, \DhlVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    public function getCapabilities()
    {
        return [\DhlVendor\Composer\Plugin\Capability\CommandProvider::class => \DhlVendor\WPDesk\Composer\Codeception\CommandProvider::class];
    }
}
