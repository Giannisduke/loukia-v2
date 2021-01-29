<?php

namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback;

use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\ErrorLogCatcher;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\RateMethod;
/**
 * Fallback is a rate when any other rates are not working. It has fixed cost that can be set by admin.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod
 */
class FallbackRateMethod implements \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\RateMethod
{
    const FIELD_ENABLE_FALLBACK = 'fallback';
    const FIELD_FALLBACK_COST = 'fallback_cost';
    const FIELD_TYPE_FALLBACK = 'fallback';
    const META_DATA_KEY = 'fallback_reason';
    /** @var bool */
    private $debug_mode;
    /**
     * @param bool $debug_mode
     */
    public function __construct($debug_mode)
    {
        $this->debug_mode = $debug_mode;
    }
    /**
     * Add rate method settings to shipment service settings.
     *
     * @param array $settings Settings from \WC_Shipping_Method
     *
     * @return array Settings with rate settings
     */
    public function add_to_settings(array $settings)
    {
        return $settings;
    }
    /**
     * Ensures that if no rates exists in method then the fallback rate is used. Should be called last, after all
     * other methods to produce fail rate..
     *
     * @param \WC_Shipping_Method $method Method to add rates.
     * @param ErrorLogCatcher $logger Special logger that can return last error.
     * @param WooCommerceShippingMetaDataBuilder $metadata_builder
     * @param WooCommerceShippingBuilder $shipment_builder Class that can build shipment from package
     *
     * @return void
     */
    public function handle_rates(\WC_Shipping_Method $method, \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\ErrorLogCatcher $logger, \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder $metadata_builder, \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingBuilder $shipment_builder)
    {
        if (empty($method->rates) && !empty('yes' === $method->get_option(self::FIELD_ENABLE_FALLBACK, 'no'))) {
            $fallback_reason = \__('There was no valid services available.', 'flexible-shipping-dhl-express');
            if ($logger->was_error()) {
                $fallback_reason = $logger->get_last_error_message();
            }
            $logger->info(\sprintf(\__('Fallback rate added with reason: %1$s', 'flexible-shipping-dhl-express'), $fallback_reason));
            $method->add_rate(['id' => $method->id . ':' . $method->instance_id . ':fallback', 'label' => $method->title, 'cost' => $method->get_option(self::FIELD_FALLBACK_COST), 'sort' => 0, 'meta_data' => [self::META_DATA_KEY => $fallback_reason]]);
        }
    }
}
