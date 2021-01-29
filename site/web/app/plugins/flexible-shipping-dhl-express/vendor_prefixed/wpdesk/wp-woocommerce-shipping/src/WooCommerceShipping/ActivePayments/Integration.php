<?php

/**
 * Active Payments Integration.
 *
 * @package WPDesk\WooCommerceShipping\ActivePayments
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\ActivePayments;

use DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Handles Active Payment integration.
 */
class Integration implements \DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const SHIPPING_METHOD_WITH_SERVICE_PARTS_COUNT = 3;
    const SHIPPING_METHOD_WITH_SERVICE_TO_COLLECTION_POINT_PARTS_COUNT = 4;
    const SHIPPING_METHOD_PARTS_DELIMITER = ':';
    const METHOD_ID_PART = 0;
    const ZONE_ID_PART = 1;
    /**
     * @var string
     */
    private $shipping_method_id;
    /**
     * Integration constructor.
     *
     * @param string $shipping_method_id .
     */
    public function __construct($shipping_method_id)
    {
        $this->shipping_method_id = $shipping_method_id;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('woocommerce_active_payments_checkout_shipping_method', array($this, 'get_shipping_method_for_active_payments_checkout'));
    }
    /**
     * Returns shipping method for checkout.
     * If shipping methods match service is removed.
     *
     * @param string $shipping_method_from_checkout .
     *
     * @return string
     *
     * @internal
     */
    public function get_shipping_method_for_active_payments_checkout($shipping_method_from_checkout)
    {
        $shipping_method_from_checkout_parts = \explode(self::SHIPPING_METHOD_PARTS_DELIMITER, $shipping_method_from_checkout);
        if ($this->is_current_shipping_method_with_service($shipping_method_from_checkout_parts) || $this->is_current_shipping_method_to_collection_point($shipping_method_from_checkout_parts)) {
            return $this->get_shipping_method_id_without_service($shipping_method_from_checkout_parts);
        } else {
            return $shipping_method_from_checkout;
        }
    }
    /**
     * Returns shipping method ID without service.
     *
     * @param string[] $shipping_method_from_checkout_parts
     *
     * @return string
     */
    private function get_shipping_method_id_without_service(array $shipping_method_from_checkout_parts)
    {
        $shipping_method_without_service_parts = array($shipping_method_from_checkout_parts[self::METHOD_ID_PART], $shipping_method_from_checkout_parts[self::ZONE_ID_PART]);
        return \implode(self::SHIPPING_METHOD_PARTS_DELIMITER, $shipping_method_without_service_parts);
    }
    /**
     * Is current shipping method with service?
     *
     * @param string[] $shipping_method_from_checkout_parts
     *
     * @return bool
     */
    private function is_current_shipping_method_with_service(array $shipping_method_from_checkout_parts)
    {
        return self::SHIPPING_METHOD_WITH_SERVICE_PARTS_COUNT === \count($shipping_method_from_checkout_parts) && $this->is_current_shipping_method($shipping_method_from_checkout_parts);
    }
    /**
     * Is current shipping method to collection point?
     *
     * @param string[] $shipping_method_from_checkout_parts
     *
     * @return bool
     */
    private function is_current_shipping_method_to_collection_point(array $shipping_method_from_checkout_parts)
    {
        return self::SHIPPING_METHOD_WITH_SERVICE_TO_COLLECTION_POINT_PARTS_COUNT === \count($shipping_method_from_checkout_parts) && $this->is_current_shipping_method($shipping_method_from_checkout_parts);
    }
    /**
     * Is current shipping method?
     *
     * @param string[] $shipping_method_from_checkout_parts
     *
     * @return bool
     */
    private function is_current_shipping_method(array $shipping_method_from_checkout_parts)
    {
        return isset($shipping_method_from_checkout_parts[self::METHOD_ID_PART]) && $shipping_method_from_checkout_parts[self::METHOD_ID_PART] === $this->shipping_method_id;
    }
}
