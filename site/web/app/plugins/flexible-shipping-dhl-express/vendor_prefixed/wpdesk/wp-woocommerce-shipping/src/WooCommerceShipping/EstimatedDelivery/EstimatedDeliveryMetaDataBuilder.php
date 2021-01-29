<?php

/**
 * Metadata builder.
 *
 * @package WPDesk\WooCommerceShipping\EstimatedDelivery
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery;

use DhlVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod;
/**
 * Can build order item metadata for estimated delivery.
 */
class EstimatedDeliveryMetaDataBuilder
{
    const ESTIMATED_DELIVERY_DATE = 'estimated_delivery_date';
    const DAYS_TO_DELIVERY_DATE = 'days_to_delivery_date';
    const BUSINESS_DAYS_IN_TRANSIT = 'business_days_in_transit';
    /**
     * Shipping method.
     *
     * @var ShippingMethod
     */
    private $shipping_method;
    /**
     * EstimatedDeliveryMetaDataBuilder constructor.
     *
     * @param ShippingMethod $shipping_method .
     */
    public function __construct(\DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod $shipping_method)
    {
        $this->shipping_method = $shipping_method;
    }
    /**
     * Get days to delivery date.
     *
     * @param \DateTimeInterface $delivery_date .
     *
     * @return int
     */
    private function get_days_to_delivery_date($delivery_date)
    {
        $date1 = \date_create(\date('Y-m-d'));
        $diff = $date1->diff($delivery_date);
        return \intval($diff->format('%R%a'));
    }
    /**
     * Append delivery dates metadata if exists.
     *
     * @param array $meta_data
     * @param SingleRate $rate
     *
     * @return array
     */
    public function append_delivery_dates_metadata_if_exists(array $meta_data, \DhlVendor\WPDesk\AbstractShipping\Rate\SingleRate $rate)
    {
        if (isset($rate->delivery_date)) {
            $meta_data[self::ESTIMATED_DELIVERY_DATE] = \date_i18n(\get_option('date_format'), $rate->delivery_date->getTimestamp());
            $meta_data[self::DAYS_TO_DELIVERY_DATE] = $this->get_days_to_delivery_date($rate->delivery_date);
        }
        if (isset($rate->business_days_in_transit)) {
            $meta_data[self::BUSINESS_DAYS_IN_TRANSIT] = $rate->business_days_in_transit;
        }
        $meta_data[\DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryDatesDisplay::DELIVERY_DATES] = $this->shipping_method->get_delivery_dates_setting($this->shipping_method);
        return $meta_data;
    }
}
