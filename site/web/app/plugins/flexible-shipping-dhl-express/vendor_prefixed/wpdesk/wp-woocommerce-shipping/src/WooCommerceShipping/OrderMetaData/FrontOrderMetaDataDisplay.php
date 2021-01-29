<?php

/**
 * Frontend meta data display.
 *
 * @package WPDesk\PluginBuilder\Plugin\Hookable
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData;

use DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can display order shipping items meta data on front end.
 */
class FrontOrderMetaDataDisplay implements \DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var SingleFrontOrderMetaDataInterpreter[]
     */
    private $interpreters = array();
    /**
     * Method ID.
     *
     * @var string
     */
    private $method_id;
    public function __construct($method_id)
    {
        $this->method_id = $method_id;
    }
    /**
     * Add front interpreter.
     *
     * @param SingleFrontOrderMetaDataInterpreter $front_interpreter .
     */
    public function add_interpreter(\DhlVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleFrontOrderMetaDataInterpreter $front_interpreter)
    {
        $this->interpreters[] = $front_interpreter;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('woocommerce_order_details_after_order_table', array($this, 'maybe_display_order_meta_for_customer'));
        \add_action('woocommerce_email_order_meta', array($this, 'maybe_display_order_meta_for_customer'));
    }
    /**
     * Maybe display order meta for customer.
     *
     * @param \WC_Abstract_Order $order
     */
    public function maybe_display_order_meta_for_customer($order)
    {
        $shipping_methods = $order->get_shipping_methods();
        foreach ($shipping_methods as $shipping_method) {
            $this->display_order_meta_for_shipping_method($shipping_method);
        }
    }
    /**
     * Display order meta for shipping method;
     *
     * @param \WC_Order_Item_Shipping $shipping_method .
     */
    private function display_order_meta_for_shipping_method($shipping_method)
    {
        foreach ($shipping_method->get_meta_data() as $single_meta_data) {
            $this->display_single_order_meta($single_meta_data, $shipping_method);
        }
    }
    /**
     * Display order meta.
     *
     * @param \WC_Meta_Data           $single_meta_data .
     * @param \WC_Order_Item_Shipping $shipping_method .
     */
    private function display_single_order_meta($single_meta_data, $shipping_method)
    {
        $data = $single_meta_data->get_data();
        $meta_key = $data['key'];
        foreach ($this->interpreters as $interpreter) {
            if ($interpreter->is_supported_key_on_front($meta_key)) {
                $interpreter->display_order_meta_on_front($meta_key, $single_meta_data, $shipping_method);
            }
        }
    }
}
