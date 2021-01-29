<?php

/**
 * Shipping method. Helper.
 *
 * @package WPDesk\WooCommerceShipping
 */
namespace DhlVendor\WPDesk\WooCommerceShipping;

use DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings as ShopSettingsInterface;
/**
 * Define some helper functions.
 */
class ShopSettings implements \DhlVendor\WPDesk\AbstractShipping\Shop\ShopSettings
{
    /**
     * Service ID.
     *
     * @var string
     */
    private $service_id;
    /**
     * ShopSettings constructor.
     *
     * @param string $service_id .
     */
    public function __construct($service_id)
    {
        $this->service_id = $service_id;
    }
    /**
     * Get countries.
     *
     * @return array
     * @throws WooCommerceNotInitializedException .
     */
    public function get_countries()
    {
        if (isset(\WC()->countries)) {
            return \WC()->countries->get_countries();
        }
        throw new \DhlVendor\WPDesk\WooCommerceShipping\WooCommerceNotInitializedException();
    }
    /**
     * Get EU countries.
     *
     * @return string[]
     * @throws WooCommerceNotInitializedException .
     */
    public function get_eu_countries()
    {
        if (isset(\WC()->countries)) {
            return \WC()->countries->get_european_union_countries();
        }
        throw new \DhlVendor\WPDesk\WooCommerceShipping\WooCommerceNotInitializedException();
    }
    /**
     * Get states.
     *
     * @param null|string $cc Country code.
     *
     * @return array|false
     * @throws WooCommerceNotInitializedException .
     */
    public function get_states($cc = null)
    {
        if (isset(\WC()->countries)) {
            return \WC()->countries->get_states($cc);
        }
        throw new \DhlVendor\WPDesk\WooCommerceShipping\WooCommerceNotInitializedException();
    }
    /**
     * Get WooCommerce country.
     *
     * @return string
     */
    public function get_origin_country()
    {
        if (isset(\WC()->countries)) {
            return \WC()->countries->get_base_country();
        }
        return '';
    }
    /**
     * Get locale.
     *
     * @return string
     */
    public function get_locale()
    {
        return \get_locale();
    }
    /**
     * Get weight unit.
     *
     * @return string
     */
    public function get_weight_unit()
    {
        return \get_option('woocommerce_weight_unit', '');
    }
    /**
     * Get WooCommerce currency.
     *
     * @return string
     */
    public function get_currency()
    {
        return \get_woocommerce_currency();
    }
    /**
     * Get default shop currency.
     *
     * @return string
     */
    public function get_default_currency()
    {
        return \get_option('woocommerce_currency', 'USD');
    }
    /**
     * Get price rounding precision.
     *
     * @return int
     */
    public function get_price_rounding_precision()
    {
        return \intval(\get_option('woocommerce_price_num_decimals', '2'));
    }
    /**
     * Is production?
     *
     * @return bool
     */
    public function is_testing()
    {
        return \apply_filters("{$this->service_id}_testing", \false);
    }
    /**
     * Is tax enabled?
     *
     * @return bool
     */
    public function is_tax_enabled()
    {
        return \wc_tax_enabled();
    }
}
