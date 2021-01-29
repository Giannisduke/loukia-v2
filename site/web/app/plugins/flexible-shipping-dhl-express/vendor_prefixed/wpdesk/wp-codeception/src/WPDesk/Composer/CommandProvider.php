<?php

namespace DhlVendor\WPDesk\Composer\Codeception;

use DhlVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use DhlVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \DhlVendor\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new \DhlVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests(), new \DhlVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests()];
    }
}
