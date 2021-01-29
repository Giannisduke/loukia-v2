<?php

namespace DhlVendor\WPDesk\AbstractShipping\Shop;

/**
 * Define some helper functions.
 *
 * @TODO: move from AbstractShipping to AbstractWooCommerceShipping ?
 */
interface ShopSettings
{
    /**
     * Get countries.
     *
     * @return string[]
     */
    public function get_countries();
    /**
     * Get EU countries.
     *
     * @return string[]
     */
    public function get_eu_countries();
    /**
     * Get states.
     *
     * @param null|string $cc Country code.
     *
     * @return array|false
     */
    public function get_states($cc = null);
    /**
     * Get WooCommerce country.
     *
     * @return string
     */
    public function get_origin_country();
    /**
     * Get locale.
     *
     * @return string
     */
    public function get_locale();
    /**
     * Get weight unit.
     *
     * @return string
     */
    public function get_weight_unit();
    /**
     * Get WooCommerce currency.
     *
     * @return string
     */
    public function get_currency();
    /**
     * Get default shop currency.
     *
     * @return string
     */
    public function get_default_currency();
    /**
     * Get price rounding precision.
     *
     * @return int
     */
    public function get_price_rounding_precision();
    /**
     * Is production?
     *
     * @return bool
     */
    public function is_testing();
    /**
     * Is tax enabled?
     *
     * @return bool
     */
    public function is_tax_enabled();
}
