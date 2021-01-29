<?php

namespace FSVendor\WPDesk\Composer\Codeception;

use FSVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use FSVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \FSVendor\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new \FSVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests(), new \FSVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests()];
    }
}
