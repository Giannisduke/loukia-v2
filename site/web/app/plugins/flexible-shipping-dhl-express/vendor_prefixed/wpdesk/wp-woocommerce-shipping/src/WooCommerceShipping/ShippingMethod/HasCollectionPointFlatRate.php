<?php

/**
 * Capability: HasFlatRate interface.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod;

/**
 * Interface for flat rate.
 */
interface HasCollectionPointFlatRate
{
    /**
     * Is flat rate enabled.
     *
     * @param \WC_Shipping_Method $shipping_method .
     *
     * @return bool
     */
    public function is_flat_rate_enabled($shipping_method);
    /**
     * Return flat rate costs.
     *
     * @param \WC_Shipping_Method $shipping_method .
     *
     * @return float
     */
    public function get_flat_rate_cost($shipping_method);
    /**
     * Returns flat rate shipping rate suffix.
     *
     * @param \WC_Shipping_Method $shipping_method .
     *
     * @return string
     */
    public function get_flat_rate_shipping_rate_suffix($shipping_method);
}
