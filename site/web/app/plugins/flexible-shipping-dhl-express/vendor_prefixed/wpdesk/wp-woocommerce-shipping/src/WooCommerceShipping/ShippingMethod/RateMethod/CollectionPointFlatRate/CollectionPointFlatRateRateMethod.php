<?php

/**
 * FlatRate rate method.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\FlatRateRateMethod
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\FlatRateRateMethod;

use DhlVendor\WPDesk\AbstractShipping\Exception\CollectionPointNotFoundException;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\CollectionPoint\CollectionPointRateMethod;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\ErrorLogCatcher;
/**
 * Can handle flat rate.
 */
class CollectionPointFlatRateRateMethod extends \DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\CollectionPoint\CollectionPointRateMethod
{
    const OPTION_FLAT_RATE_COSTS = 'flat_rate_costs';
    /**
     * Flat rate costs.
     *
     * @var float
     */
    private $flat_rate_costs;
    /**
     * Shipping rate suffix.
     *
     * @var string
     */
    private $shipping_rate_suffix;
    /**
     * FlatRateRateMethod constructor.
     *
     * @param float $flat_rate_costs .
     * @param string $shipping_rate_suffix .
     */
    public function __construct($flat_rate_costs, $shipping_rate_suffix)
    {
        $this->flat_rate_costs = $flat_rate_costs;
        $this->shipping_rate_suffix = $shipping_rate_suffix;
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
        $this->add_rates_to_collection_point($method, $metadata_builder, $shipment_builder);
    }
    /**
     * Rate shipment.
     *
     * @param \WC_Shipping_Method $method Method.
     * @param WooCommerceShippingMetaDataBuilder $meta_data_builder Meta data builder.
     * @param WooCommerceShippingBuilder $shipment_builder Class that can build shipment from package
     */
    private function add_rates_to_collection_point(\WC_Shipping_Method $method, $meta_data_builder, \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingBuilder $shipment_builder)
    {
        $service_id = $method->id;
        $collection_point = $this->get_collection_point_for_rates($shipment_builder->get_woocommerce_package(), $method);
        /** @var WooCommerceShippingMetaDataBuilder $meta_data_builder */
        $meta_data_builder = \apply_filters("{$service_id}_meta_data_builder", $meta_data_builder, $method);
        $meta_data = [];
        if (isset($meta_data_builder)) {
            if (null !== $collection_point) {
                $meta_data = $meta_data_builder->build_meta_data_to_collection_point($collection_point);
            }
        }
        if (0 === \intval($method->instance_id)) {
            $rate_id = $method->id . ':' . $this->shipping_rate_suffix;
        } else {
            $rate_id = $method->id . ':' . $method->instance_id . ':' . $this->shipping_rate_suffix;
        }
        $method->add_rate(['id' => $rate_id, 'label' => $method->title, 'cost' => $this->flat_rate_costs, 'sort' => 0, 'meta_data' => $meta_data]);
    }
}
