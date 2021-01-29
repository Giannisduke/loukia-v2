<?php

/**
 * Package address: WooCommerceAddressReceiver.
 *
 * @package WPDesk\ShippingBuilder\Address
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder;

use DhlVendor\WPDesk\AbstractShipping\Shipment\Address;
/**
 * Get receiver address from WooCommerce Package
 *
 * @package WPDesk\ShippingBuilder\Address
 */
class WooCommerceAddressReceiver implements \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\AddressProvider
{
    /**
     * Package data.
     *
     * @var array
     */
    private $package;
    /**
     * WooCommerceAddressReceiver constructor.
     *
     * @param array $package Package data.
     */
    public function __construct(array $package)
    {
        $this->package = $package;
    }
    /**
     * Get value from package destination.
     *
     * @param string $name    Key.
     * @param string $default Default value.
     *
     * @return string
     */
    private function get_destination_value($name, $default = '')
    {
        if (isset($this->package['destination'][$name])) {
            return $this->package['destination'][$name];
        }
        return $default;
    }
    /**
     * Get address.
     *
     * @return Address
     */
    public function get_address()
    {
        $address = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Address();
        $address->address_line1 = $this->get_destination_value('address');
        $address->address_line2 = $this->get_destination_value('address2');
        $address->city = $this->get_destination_value('city');
        $address->postal_code = $this->get_destination_value('postcode');
        $address->country_code = $this->get_destination_value('country');
        $address->state_code = $this->get_destination_value('state');
        return $address;
    }
}
