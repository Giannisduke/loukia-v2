<?php

/**
 * Assets.
 *
 * @package WPDesk\WooCommerceShipping
 */
namespace DhlVendor\WPDesk\WooCommerceShipping;

use DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Loads assets.
 *
 */
class Assets implements \DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const CUSTOM_SERVICES_CHECKBOX_CLASS = 'wpdesk_wc_shipping_custom_service_checkbox';
    /**
     * Scripts version.
     *
     * @var string
     */
    private $scripts_version = '14';
    /**
     * Assets URL.
     *
     * @var string
     */
    private $assets_url = '';
    /**
     * Assets URL.
     *
     * @var string
     */
    private $assets_suffix = '';
    /**
     * Assets constructor.
     *
     * @param string $assets_url .
     * @param string $assets_suffix .
     */
    public function __construct($assets_url, $assets_suffix)
    {
        $this->assets_url = $assets_url;
        $this->assets_suffix = $assets_suffix;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        \add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    /**
     * Enqueue admin scripts.
     */
    public function admin_enqueue_scripts()
    {
        global $current_screen;
        if ('woocommerce_page_wc-settings' === $current_screen->id) {
            $suffix = \defined('SCRIPT_DEBUG') && \SCRIPT_DEBUG ? '' : '.min';
            $handle = 'wpdesk_wc_shipping_' . $this->assets_suffix;
            \wp_register_style($handle, \trailingslashit($this->assets_url) . 'css/admin' . $suffix . '.css', array(), $this->scripts_version);
            \wp_enqueue_style($handle);
        }
    }
    /**
     * Enqueue scripts.
     */
    public function enqueue_scripts()
    {
        $suffix = \defined('SCRIPT_DEBUG') && \SCRIPT_DEBUG ? '' : '.min';
        $handle = 'wpdesk_wc_shipping_notices_' . $this->assets_suffix;
        \wp_register_script($handle, \trailingslashit($this->assets_url) . 'js/notices' . $suffix . '.js', array(), $this->scripts_version, \true);
        \wp_enqueue_script($handle);
        \wp_register_style($handle, \trailingslashit($this->assets_url) . 'css/notices' . $suffix . '.css', array(), $this->scripts_version);
        \wp_enqueue_style($handle);
    }
}
