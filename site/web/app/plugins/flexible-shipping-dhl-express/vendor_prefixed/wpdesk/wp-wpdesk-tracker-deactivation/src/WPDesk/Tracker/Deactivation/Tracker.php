<?php

namespace DhlVendor\WPDesk\Tracker\Deactivation;

use DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can track plugin deactivation.
 */
class Tracker implements \DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const HOOK_SUFFIX = 'plugins.php';
    /**
     * @var PluginData
     */
    protected $plugin_data;
    /**
     * @var Scripts
     */
    private $scripts;
    /**
     * @var Thickbox
     */
    private $thickbox;
    /**
     * @var AjaxDeactivationDataHandler
     */
    private $ajax;
    /**
     * DeactivationTracker constructor.
     *
     * @param PluginData $plugin_data .
     * @param Scripts $scripts .
     * @param Thickbox $thickbox .
     * @param AjaxDeactivationDataHandler $ajax
     */
    public function __construct(\DhlVendor\WPDesk\Tracker\Deactivation\PluginData $plugin_data, \DhlVendor\WPDesk\Tracker\Deactivation\Scripts $scripts, \DhlVendor\WPDesk\Tracker\Deactivation\Thickbox $thickbox, \DhlVendor\WPDesk\Tracker\Deactivation\AjaxDeactivationDataHandler $ajax)
    {
        $this->plugin_data = $plugin_data;
        $this->scripts = $scripts;
        $this->thickbox = $thickbox;
        $this->ajax = $ajax;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('admin_print_footer_scripts-' . self::HOOK_SUFFIX, [$this, 'printDeactivationScripts']);
        \add_action('admin_footer-' . self::HOOK_SUFFIX, [$this, 'printDeactivationThickbox']);
        $this->ajax->hooks();
    }
    /**
     * Print deactivation scripts.
     */
    public function printDeactivationScripts()
    {
        echo $this->scripts->getContent();
    }
    /**
     * Print deactivation thickbox.
     */
    public function printDeactivationThickbox()
    {
        echo $this->thickbox->getContent();
    }
}
