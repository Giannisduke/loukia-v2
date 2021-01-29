<?php

namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Standard;

use DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRate;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\AddressProvider;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\ErrorLogCatcher;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\RateMethod;
/**
 * Rate method that uses Shipping service to create live rates in method.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Standard\
 */
class StandardServiceRateMethod implements \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\RateMethod
{
    /** @var CanRate */
    private $rate_provider;
    /**
     * StandardServiceRateMethod constructor.
     *
     * @param CanRate         $service Service that provides the rates.
     */
    public function __construct(\DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRate $service)
    {
        $this->rate_provider = $service;
    }
    /**
     * Adds shipment rates to method.
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
        try {
            $rates_count = \count($method->rates);
            $this->add_rates($this->rate_provider, $method, $metadata_builder, $shipment_builder);
            if ($rates_count === \count($method->rates)) {
                $logger->info(\__('No rates added from standard service rates!', 'flexible-shipping-dhl-express'));
            }
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
        }
    }
    /**
     * Rate shipment.
     *
     * @param CanRate $service Service.
     * @param \WC_Shipping_Method $method Method.
     * @param WooCommerceShippingMetaDataBuilder $meta_data_builder Meta data builder.
     * @param WooCommerceShippingBuilder $shipping_builder Class that can build shipment from package
     */
    private function add_rates(\DhlVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRate $service, \WC_Shipping_Method $method, \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder $meta_data_builder, \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingBuilder $shipping_builder)
    {
        $service_id = $method->id;
        $service_settings = \apply_filters("{$service_id}_settings_before_rate", new \DhlVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray($method->settings + $method->instance_settings), $method);
        if ($service->is_rate_enabled($service_settings)) {
            $shipment = \apply_filters("{$service_id}_shipment_before_rate", $shipping_builder->build_shipment(), $method);
            $rates_from_response = \apply_filters("{$service_id}_rates", $service->rate_shipment($service_settings, $shipment), $method);
            $meta_data_builder = \apply_filters("{$service_id}_meta_data_builder", $meta_data_builder, $method);
            $this->add_rates_from_response($method, $shipment, $rates_from_response, $meta_data_builder);
        }
    }
    /**
     * Add Woocommerce shipping rates.
     *
     * @param \WC_Shipping_Method $method Method.
     * @param Shipment $shipment Shipment.
     * @param ShipmentRating $shipment_ratings Shipment rates.
     * @param WooCommerceShippingMetaDataBuilder $meta_data_builder Meta data builder.
     */
    private function add_rates_from_response(\WC_Shipping_Method $method, \DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment, \DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating $shipment_ratings, \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder $meta_data_builder)
    {
        foreach ($shipment_ratings->get_ratings() as $rate) {
            if (isset($meta_data_builder)) {
                $meta_data = $meta_data_builder->build_meta_data_for_rate($rate, $shipment);
            } else {
                $meta_data = [];
            }
            $method->add_rate(['id' => $method->id . ':' . $method->instance_id . ':' . $rate->service_type, 'label' => $rate->service_name, 'cost' => $rate->total_charge->amount, 'sort' => 0, 'meta_data' => $meta_data]);
        }
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
}
