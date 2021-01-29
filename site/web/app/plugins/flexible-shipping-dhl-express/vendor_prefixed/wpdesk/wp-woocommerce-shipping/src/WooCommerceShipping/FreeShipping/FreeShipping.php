<?php

/**
 * Free Shipping.
 *
 * @package WPDesk\WooCommerceShipping\FreeShipping
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\FreeShipping;

use WC_Cart;
use WC_Shipping_Method;
use DhlVendor\WPDesk\WooCommerceShipping\DisplayNoticeLogger;
/**
 * Can apply handling fees to price.
 */
class FreeShipping
{
    /**
     * @var WC_Shipping_Method .
     */
    private $shipping_method;
    /**
     * @var DisplayNoticeLogger .
     */
    private $logger;
    /**
     * FreeShipping constructor.
     *
     * @param WC_Shipping_Method  $shipping_method .
     * @param DisplayNoticeLogger $logger
     */
    public function __construct($shipping_method, $logger)
    {
        $this->shipping_method = $shipping_method;
        $this->logger = $logger;
    }
    /**
     * Check can apply free shipping.
     *
     * @param float $subtotal Input Cart.
     *
     * @return bool
     */
    public function can_apply($subtotal)
    {
        return $subtotal >= $this->get_free_shipping_amount();
    }
    /**
     * @param bool  $is_applied .
     * @param float $subtotal   .
     */
    public function debug($is_applied, $subtotal)
    {
        $label = $is_applied ? \__('Cart value exceeds %s. Free shipping has been applied.', 'flexible-shipping-dhl-express') : \__('Cart value doesn\'t exceed %s. Free shipping hasn\'t been applied.', 'flexible-shipping-dhl-express');
        $this->logger->debug(\sprintf($label, \wc_price($this->get_free_shipping_amount())), array(\__('Subtotal', 'flexible-shipping-dhl-express') => $subtotal, \__('Free Shipping Amount', 'flexible-shipping-dhl-express') => $this->get_free_shipping_amount()));
    }
    /**
     * @return bool
     */
    public function is_enabled()
    {
        return 'yes' === $this->shipping_method->get_option(\DhlVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields::FIELD_STATUS, 'no');
    }
    /**
     * @return float
     */
    private function get_free_shipping_amount()
    {
        return (float) $this->shipping_method->get_option(\DhlVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields::FIELD_AMOUNT);
    }
}
