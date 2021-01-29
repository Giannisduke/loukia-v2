<?php

/**
 * Capability: HasEstimatedDeliveryDates interface.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod;

use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod;
/**
 * Interface for handling fees.
 */
interface HasEstimatedDeliveryDates
{
    /**
     * Gets delivery dates setting value.
     *
     * @param \WC_Shipping_Method $shipping_method .
     *
     * @return string
     */
    public function get_delivery_dates_setting($shipping_method);
    /**
     * Get maximum transit time setting.
     *
     * @param \WC_Shipping_Method $shipping_method
     *
     * @return string
     */
    public function get_maximum_transit_time_setting($shipping_method);
    /**
     * Should exclude rate with maximum transit time.
     *
     * @param \WC_Shipping_Method $shipping_method .
     * @param array $shipping_rate_meta_data .
     *
     * @return bool
     */
    public function should_exclude_rate_with_maximum_transit_time($shipping_method, array $shipping_rate_meta_data);
}
