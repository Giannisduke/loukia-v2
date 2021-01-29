<?php

/**
 * Collection Points: CheckoutAddress class.
 *
 * @package WPDesk\WooCommerceShipping\CollectionPoints
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints;

use DhlVendor\WPDesk\AbstractShipping\Shipment\Address;
/**
 * Creates Address object for Checkout.
 */
class CheckoutAddress
{
    /**
     * $_REQUEST data sent by Ajax.
     *
     * @var array
     */
    private $request;
    /**
     * Address of destination.
     *
     * @var array
     */
    private $destination;
    /**
     * CheckoutAddress constructor.
     *
     * @param array $request $_REQUEST data sent by Ajax.
     * @param array $destination Optionally address of destination.
     */
    public function __construct(array $request, array $destination = array())
    {
        $this->request = $request;
        $this->destination = $destination;
    }
    /**
     * Prepares Address object.
     *
     * @return Address
     */
    public function prepare_address()
    {
        $address = new \DhlVendor\WPDesk\AbstractShipping\Shipment\Address();
        $address->country_code = $this->get_country_code();
        $address->postal_code = $this->get_post_code();
        $address->city = $this->get_city();
        $address->address_line1 = $this->get_address_1();
        $address->address_line2 = $this->get_address_2();
        return $address;
    }
    /**
     * Get address value.
     *
     * @param string $request_key Value key from $_REQUEST request.
     * @param string $destination_key Value key from array of destination address.
     * @param string $customer_method Method name of WC_Customer class.
     *
     * @return string
     */
    private function get_address_value($request_key, $destination_key, $customer_method)
    {
        if (isset($this->destination[$destination_key]) && !empty($this->destination[$destination_key])) {
            return $this->destination[$destination_key];
        }
        if (empty($this->request[$request_key])) {
            $value = \WC()->customer->{$customer_method}();
        } else {
            $value = \sanitize_text_field($this->request[$request_key]);
        }
        return $value;
    }
    /**
     * Get country code.
     *
     * @return string
     */
    private function get_country_code()
    {
        $value = $this->get_address_value('s_country', 'country', 'get_shipping_country');
        if (empty($value)) {
            $value = \WC()->countries->get_base_country();
        }
        return $value;
    }
    /**
     * Get post code.
     *
     * @return string
     */
    private function get_post_code()
    {
        $value = $this->get_address_value('s_postcode', 'postcode', 'get_shipping_postcode');
        if (empty($value)) {
            $value = \get_option('woocommerce_store_postcode', '');
        }
        return $value;
    }
    /**
     * Get city.
     *
     * @return string
     */
    private function get_city()
    {
        return $this->get_address_value('s_city', 'city', 'get_shipping_city');
    }
    /**
     * Get address line 1.
     *
     * @return string
     */
    private function get_address_1()
    {
        return $this->get_address_value('s_address', 'address', 'get_shipping_address');
    }
    /**
     * Get address line 2.
     *
     * @return string
     */
    private function get_address_2()
    {
        return $this->get_address_value('s_address_2', 'address_2', 'get_shipping_address_2');
    }
}
