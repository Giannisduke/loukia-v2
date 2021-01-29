<?php

/**
 * Shipping builder: ShippingBuilder.
 *
 * @package WPDesk\ShippingBuilder
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder;

use DhlVendor\WPDesk\AbstractShipping\Rate\Money;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Client;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Item;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Package;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Weight;
/**
 * Build raw shipping data from WooCommerce
 *
 * @package WPDesk\ShippingBuilder
 */
class WooCommerceShippingBuilder
{
    /**
     * Sender address
     *
     * @var AddressProvider
     */
    private $sender_address;
    /**
     * Sender address
     *
     * @var AddressProvider
     */
    private $receiver_address;
    /**
     * WooCommerce Package
     *
     * @var array
     */
    protected $package;
    /**
     * Dimension unit.
     *
     * @var string
     */
    private $dimension_unit;
    /**
     * Weight unit.
     *
     * @var string
     */
    private $weight_unit;
    /**
     * Currency for items.
     *
     * @var string
     */
    protected $currency;
    /**
     * Rounding precision.
     *
     * @var int
     */
    private $rounding_precision;
    /**
     * @param AddressProvider $sender_address Sender address.
     */
    public function set_sender_address(\DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\AddressProvider $sender_address)
    {
        $this->sender_address = $sender_address;
    }
    /**
     * @param AddressProvider $receiver_address Receiver address.
     */
    public function set_receiver_address(\DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\AddressProvider $receiver_address)
    {
        $this->receiver_address = $receiver_address;
    }
    /**
     * @param string $dimension_unit Dimension unit.
     */
    public function set_dimension_unit($dimension_unit)
    {
        $this->dimension_unit = $dimension_unit;
    }
    /**
     * @param string $weight_unit Weight unit.
     */
    public function set_weight_unit($weight_unit)
    {
        $this->weight_unit = $weight_unit;
    }
    /**
     * @param string $currency Currency for items.
     */
    public function set_currency($currency)
    {
        $this->currency = $currency;
    }
    /**
     * @param int $rounding_precision Rounding precision.
     */
    public function set_rounding_precision($rounding_precision)
    {
        $this->rounding_precision = $rounding_precision;
    }
    /**
     * Sets WooCommerce Package to build.
     *
     * @param array $package Package.
     */
    public function set_woocommerce_package(array $package)
    {
        $this->package = $package;
    }
    /**
     * Returns WooCommerce package that is used to build shipment.
     *
     * @return array
     *
     * @deprecated Try not to use it. Better use built shipment.
     */
    public function get_woocommerce_package()
    {
        return $this->package;
    }
    /**
     * Get dimension unit.
     *
     * @return string
     */
    protected function get_woocommerce_dimension_unit()
    {
        switch ($this->dimension_unit) {
            case 'mm':
                return \DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_MM;
            case 'cm':
                return \DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_CM;
            case 'm':
                return \DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_M;
            case 'in':
                return \DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_IN;
        }
        return $this->dimension_unit;
    }
    /**
     * Get weight unit.
     *
     * @return string
     */
    protected function get_woocommerce_weight_unit()
    {
        switch ($this->weight_unit) {
            case 'g':
                return \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_G;
            case 'kg':
                return \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_KG;
            case 'lb':
                return \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB;
        }
        return $this->weight_unit;
    }
    /**
     * Get weight.
     *
     * @param array $package_item Package item.
     *
     * @return \WPDesk\AbstractShipping\Shipment\Weight
     */
    protected function get_weight($package_item)
    {
        $weight = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight();
        $weight->weight = \floatval($package_item['data']->get_weight());
        $weight->weight_unit = $this->get_woocommerce_weight_unit();
        return $weight;
    }
    /**
     * Get item name.
     *
     * @param \WC_Product $package_item_data .
     *
     * @return string
     */
    private function get_item_name($package_item_data)
    {
        return $package_item_data->get_name();
    }
    /**
     * Add package items.
     *
     * @param array $package_item Package item.
     *
     * @return Item
     */
    protected function add_package_item($package_item)
    {
        $item = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Item();
        $item->name = $this->get_item_name($package_item['data']);
        $item->dimensions = $this->get_dimensions($package_item);
        $item->weight = $this->get_weight($package_item);
        $item->declared_value = new \DhlVendor\WPDesk\AbstractShipping\Rate\Money();
        $item->declared_value->amount = \round($package_item['line_total'] / $package_item['quantity'], $this->rounding_precision);
        $item->declared_value->currency = $this->currency;
        return $item;
    }
    /**
     * Get dimension.
     *
     * @param array $package_item Package item.
     *
     * @return \WPDesk\AbstractShipping\Shipment\Dimensions
     */
    protected function get_dimensions($package_item)
    {
        $dimension = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Dimensions();
        $dimension->dimensions_unit = $this->get_woocommerce_dimension_unit();
        $dimension->length = $package_item['data']->get_length();
        $dimension->width = $package_item['data']->get_width();
        $dimension->height = $package_item['data']->get_height();
        return $dimension;
    }
    /**
     * Get package.
     *
     * @return Package[]
     */
    protected function get_packages()
    {
        $shipping_package = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Package();
        $package_weight = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight();
        $package_weight->weight_unit = $this->get_woocommerce_weight_unit();
        $package_weight->weight = 0.0;
        foreach ($this->package['contents'] as $item) {
            for ($i = 0; $i < $item['quantity']; $i++) {
                $shipping_item = $this->add_package_item($item);
                $package_weight->weight += $shipping_item->weight->weight;
                $shipping_package->items[] = $shipping_item;
            }
        }
        $shipping_package->weight = $package_weight;
        return [$shipping_package];
    }
    /**
     * Return shipping. To work correctly you have set all the required elements trough setters.
     *
     * @return \WPDesk\AbstractShipping\Shipment\Shipment;
     */
    public function build_shipment()
    {
        $shipment = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Shipment();
        $ship_from = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Client();
        $ship_from->address = $this->sender_address->get_address();
        $ship_to = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Client();
        $ship_to->address = $this->receiver_address->get_address();
        $shipment->ship_from = $ship_from;
        $shipment->ship_to = $ship_to;
        $shipment->packages = $this->get_packages();
        return $shipment;
    }
}
