<?php

namespace DhlVendor\WPDesk\View\Resolver;

use DhlVendor\WPDesk\View\Renderer\Renderer;
use DhlVendor\WPDesk\View\Resolver\Exception\CanNotResolve;
/**
 * This resolver never finds the file
 *
 * @package WPDesk\View\Resolver
 */
class NullResolver implements \DhlVendor\WPDesk\View\Resolver\Resolver
{
    public function resolve($name, \DhlVendor\WPDesk\View\Renderer\Renderer $renderer = null)
    {
        throw new \DhlVendor\WPDesk\View\Resolver\Exception\CanNotResolve("Null Cannot resolve");
    }
}
