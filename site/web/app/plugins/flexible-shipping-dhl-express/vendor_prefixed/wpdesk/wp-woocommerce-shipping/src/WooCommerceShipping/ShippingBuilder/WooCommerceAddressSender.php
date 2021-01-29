<?php

/**
 * WooCommerce shipping address: WooCommerceAddressSender.
 *
 * @package WPDesk\ShippingBuilder\Address
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder;

use DhlVendor\WPDesk\AbstractShipping\Shipment\Address;
/**
 * Get sender address from WooCommerce settings
 *
 * @package WPDesk\ShippingBuilder\Address
 */
class WooCommerceAddressSender implements \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\AddressProvider
{
    /**
     * Get address.
     *
     * @return Address
     */
    public function get_address()
    {
        $address = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Address();
        $address->address_line1 = \get_option('woocommerce_store_address', '');
        $address->address_line2 = \get_option('woocommerce_store_address_2', '');
        $address->city = \get_option('woocommerce_store_city', '');
        $address->postal_code = \get_option('woocommerce_store_postcode', '');
        $woocommerce_default_country = \explode(':', \get_option('woocommerce_default_country', ''));
        $address->country_code = isset($woocommerce_default_country[0]) ? $woocommerce_default_country[0] : '';
        $address->state_code = isset($woocommerce_default_country[1]) ? $woocommerce_default_country[1] : '';
        return $address;
    }
}
