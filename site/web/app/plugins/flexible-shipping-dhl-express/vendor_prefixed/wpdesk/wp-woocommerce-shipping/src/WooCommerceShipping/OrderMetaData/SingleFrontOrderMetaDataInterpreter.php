<?php

/**
 * Front order meta data interpreter.
 *
 * @package WPDesk\WooCommerceShipping\OrderMetaData
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData;

/**
 * Interface for front meta data interpreters.
 */
interface SingleFrontOrderMetaDataInterpreter
{
    /**
     * Display order meta.
     *
     * @param string         $display_key .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return void
     */
    public function display_order_meta_on_front($display_key, $meta, $order_item);
    /**
     * Is supported key on front?
     *
     * @param string $display_key .
     *
     * @return bool
     */
    public function is_supported_key_on_front($display_key);
}
