<?php

/**
 * Shipping address: SettingsDefinition.
 *
 * @package WPDesk\ShippingBuilder\Address
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder;

/**
 * Interface for Shipping address.
 *
 * @package WPDesk\AbstractShipping\Address
 */
interface AddressProvider
{
    /**
     * Get address.
     *
     * @return \WPDesk\AbstractShipping\Shipment\Address
     */
    public function get_address();
}
