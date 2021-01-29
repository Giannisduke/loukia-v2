<?php

/**
 * Admin order meta data interpreter.
 *
 * @package WPDesk\WooCommerceShipping\OrderMetaData
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData;

/**
 * Interface for admin meta data interpreters.
 */
interface SingleAdminOrderMetaDataInterpreter
{
    /**
     * Get meta key on admin order edit page.
     *
     * @param string         $display_key .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_display_key($display_key, $meta, $order_item);
    /**
     * Get meta value on admin order edit page.
     *
     * @param string         $display_value .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_display_value($display_value, $meta, $order_item);
    /**
     * Is supported key?
     *
     * @param string $display_key .
     *
     * @return bool
     */
    public function is_supported_key_on_admin($display_key);
}
