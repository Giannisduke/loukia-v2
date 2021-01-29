<?php

/**
 * Delivery dates trait.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\Traits
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits;

use DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryDatesDisplay;
use DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryMetaDataBuilder;
/**
 * Handles delivery dates functionality.
 */
trait DeliveryDatesTrait
{
    /**
     * Gets delivery dates setting value.
     *
     * @param \WC_Shipping_Method $shipping_method .
     *
     * @return string
     */
    public function get_delivery_dates_setting($shipping_method)
    {
        return $shipping_method->get_option(\DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryDatesDisplay::DELIVERY_DATES, 'none');
    }
    /**
     * Get maximum transit time setting.
     *
     * @param \WC_Shipping_Method $shipping_method
     *
     * @return string
     */
    public function get_maximum_transit_time_setting($shipping_method)
    {
        return $shipping_method->get_option('maximum_transit_time', '');
    }
    /**
     * Should exclude rate with maximum transit time.
     *
     * @param \WC_Shipping_Method $shipping_method .
     * @param array $shipping_rate_meta_data .
     *
     * @return bool
     */
    public function should_exclude_rate_with_maximum_transit_time($shipping_method, array $shipping_rate_meta_data)
    {
        $maximum_transit_time_setting = $this->get_maximum_transit_time_setting($shipping_method);
        if ('' !== $maximum_transit_time_setting && isset($shipping_rate_meta_data[\DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryMetaDataBuilder::BUSINESS_DAYS_IN_TRANSIT])) {
            $maximum_transit_time_setting = \intval($maximum_transit_time_setting);
            $business_days_in_transit = \intval($shipping_rate_meta_data[\DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryMetaDataBuilder::BUSINESS_DAYS_IN_TRANSIT]);
            if ($business_days_in_transit > $maximum_transit_time_setting) {
                return \true;
            }
        }
        return \false;
    }
}
