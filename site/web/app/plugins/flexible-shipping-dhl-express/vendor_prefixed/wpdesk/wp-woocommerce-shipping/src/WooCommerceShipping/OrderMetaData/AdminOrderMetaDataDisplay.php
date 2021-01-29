<?php

/**
 * Admin meta data display.
 *
 * @package WPDesk\PluginBuilder\Plugin\Hookable
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData;

use DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryDatesDisplay;
use DhlVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryMetaDataBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
use DhlVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\FallbackAdminMetaDataInterpreter;
use DhlVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\PackedPackagesAdminMetaDataInterpreter;
/**
 * Can display order shipping items meta data on admin pages.
 */
class AdminOrderMetaDataDisplay implements \DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var SingleAdminOrderMetaDataInterpreter[]
     */
    private $interpreters = array();
    /**
     * Hidden order meta keys.
     *
     * @var array
     */
    private $hidden_order_meta_keys = array();
    /**
     * Method ID.
     *
     * @var string
     */
    private $method_id;
    /**
     * AdminOrderMetaDataDisplay constructor.
     *
     * @param string $method_id .
     */
    public function __construct($method_id)
    {
        $this->method_id = $method_id;
    }
    /**
     * Add hidden order item meta key.
     *
     * @param $meta_key
     */
    public function add_hidden_order_item_meta_key($meta_key)
    {
        $this->hidden_order_meta_keys[] = $meta_key;
    }
    /**
     * Add admin interpreter.
     *
     * @param SingleAdminOrderMetaDataInterpreter $admin_interpreter .
     */
    public function add_interpreter(\DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreter $admin_interpreter)
    {
        $this->interpreters[] = $admin_interpreter;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_filter('woocommerce_order_item_display_meta_key', array($this, 'get_order_meta_key_on_admin_order_edit_page'), 10, 3);
        \add_filter('woocommerce_order_item_display_meta_value', array($this, 'get_order_meta_value_on_admin_order_edit_page'), 10, 3);
        \add_filter('woocommerce_hidden_order_itemmeta', array($this, 'add_hidden_order_item_meta_keys_to_woocommerce'), 10, 3);
    }
    /**
     * Add hidden order item meta keys to WooCommerce.
     *
     * @param array $hidden_meta_keys .
     *
     * @return array
     */
    public function add_hidden_order_item_meta_keys_to_woocommerce($hidden_meta_keys)
    {
        foreach ($this->hidden_order_meta_keys as $meta_key) {
            $hidden_meta_keys[] = $meta_key;
        }
        return $hidden_meta_keys;
    }
    /**
     * Display meta key.
     *
     * @param string         $display_key .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_order_meta_key_on_admin_order_edit_page($display_key, $meta, $order_item)
    {
        if ($order_item instanceof \WC_Order_Item_Shipping && $meta instanceof \WC_Meta_Data) {
            if ($order_item->get_method_id() === $this->method_id) {
                foreach ($this->interpreters as $interpreter) {
                    if ($interpreter->is_supported_key_on_admin($display_key)) {
                        return $interpreter->get_display_key($display_key, $meta, $order_item);
                    }
                }
            }
        }
        return $display_key;
    }
    /**
     * Display meta value.
     *
     * @param string         $display_value .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_order_meta_value_on_admin_order_edit_page($display_value, $meta, $order_item)
    {
        if ($order_item instanceof \WC_Order_Item_Shipping && $meta instanceof \WC_Meta_Data) {
            if ($order_item->get_method_id() === $this->method_id) {
                foreach ($this->interpreters as $interpreter) {
                    $data = $meta->get_data();
                    if ($interpreter->is_supported_key_on_admin($data['key'])) {
                        return $interpreter->get_display_value($display_value, $meta, $order_item);
                    }
                }
            }
        }
        return $display_value;
    }
}
