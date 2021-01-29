<?php

/**
 * Meta data unchanged key and value methods.
 *
 * @package WPDesk\WooCommerceShipping\OrderMetaData
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData;

/**
 * Can return unchanged meta key and value.
 */
trait AdminMetaDataUnchangedTrait
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
    public function get_display_key($display_key, $meta, $order_item)
    {
        return $display_key;
    }
    /**
     * Get meta value on admin order edit page.
     *
     * @param string         $display_value .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_display_value($display_value, $meta, $order_item)
    {
        return $display_value;
    }
}
