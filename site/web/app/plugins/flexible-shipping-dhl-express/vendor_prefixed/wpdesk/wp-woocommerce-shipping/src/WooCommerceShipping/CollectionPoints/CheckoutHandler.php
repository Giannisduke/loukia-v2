<?php

/**
 * Collection Points: CheckoutHandler class.
 *
 * @package WPDesk\WooCommerceShipping\CollectionPoints
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints;

use DhlVendor\WPDesk\AbstractShipping\CollectionPoints\CollectionPoint;
use DhlVendor\WPDesk\AbstractShipping\CollectionPointCapability\CollectionPointsProvider;
use DhlVendor\WPDesk\AbstractShipping\Exception\CollectionPointNotFoundException;
use DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use DhlVendor\WPDesk\View\Renderer\Renderer;
use DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
/**
 * Handles collection points on checkout.
 *
 * @package WPDesk\CustomFields
 */
class CheckoutHandler implements \DhlVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var CollectionPointsProvider
     */
    private $collection_points_provider;
    /**
     * Select field available.
     *
     * @var bool
     */
    private $select_field_available;
    /**
     * Method ID.
     *
     * @var string
     */
    private $method_id;
    /**
     * Renderer.
     *
     * @var Renderer
     */
    private $renderer;
    /**
     * Field label.
     *
     * @var string
     */
    private $field_label;
    /**
     * Unavailable points label.
     *
     * @var string
     */
    private $unavailable_points_label;
    /**
     * Field description.
     *
     * @var string
     */
    private $field_description;
    /**
     * CheckoutHandler constructor.
     *
     * @param CollectionPointsProvider $collection_points_provider .
     * @param string                   $method_id .
     * @param Renderer                 $renderer .
     * @param string                   $field_label .
     * @param string                   $unavailable_points_label .
     * @param string                   $field_description .
     * @param bool                     $select_field_available .
     */
    public function __construct(\DhlVendor\WPDesk\AbstractShipping\CollectionPointCapability\CollectionPointsProvider $collection_points_provider, $method_id, \DhlVendor\WPDesk\View\Renderer\Renderer $renderer, $field_label, $unavailable_points_label, $field_description, $select_field_available = \false)
    {
        $this->collection_points_provider = $collection_points_provider;
        $this->select_field_available = $select_field_available;
        $this->method_id = $method_id;
        $this->renderer = $renderer;
        $this->field_label = $field_label;
        $this->unavailable_points_label = $unavailable_points_label;
        $this->field_description = $field_description;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('woocommerce_review_order_after_shipping', array($this, 'maybe_display_collection_points_field'));
        \add_action('woocommerce_checkout_update_order_review', array($this, 'force_shipping_recalculation_on_collection_point_change'));
    }
    /**
     * Force shipping recalculation on collection point change.
     *
     * @param array $unparsed_post_data Post data.
     */
    public function force_shipping_recalculation_on_collection_point_change($unparsed_post_data)
    {
        \parse_str($unparsed_post_data, $post_data);
        $checkout_field_name = \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutField::prepare_field_name_from_shipping_method_id($this->method_id);
        if (isset($post_data[$checkout_field_name])) {
            if ($post_data[$checkout_field_name] !== $this->get_collection_point_from_session($checkout_field_name, '')) {
                $this->force_shipping_recalculation();
            }
        }
    }
    /**
     * Force shipping recalculation.
     */
    private function force_shipping_recalculation()
    {
        /*
         * Force shipping recalculation!
         * https://stackoverflow.com/a/45763102
         */
        foreach (\WC()->cart->get_cart() as $key => $value) {
            \WC()->cart->set_quantity($key, $value['quantity'] + 1);
            \WC()->cart->set_quantity($key, $value['quantity']);
            break;
        }
    }
    /**
     * @return array
     */
    private function get_request()
    {
        return $_REQUEST;
    }
    /**
     * Prepare post data.
     *
     * @return array
     */
    private function prepare_post_data()
    {
        if (!empty($_REQUEST['post_data'])) {
            \parse_str($_REQUEST['post_data'], $post_data);
        } else {
            $post_data = array();
        }
        return $post_data;
    }
    /**
     * Get collection point from posted data.
     *
     * @param array  $post_data .
     * @param string $destination_country .
     *
     * @return CollectionPoint|null
     */
    private function get_collection_point_from_posted_data_or_session(array $post_data, $destination_country)
    {
        $collection_point = null;
        $checkout_field_name = \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutField::prepare_field_name_from_shipping_method_id($this->method_id);
        $selected_collection_point = '';
        if (isset($post_data[$checkout_field_name])) {
            $selected_collection_point = \sanitize_text_field($post_data[$checkout_field_name]);
        } else {
            $selected_collection_point = $this->get_collection_point_from_session($checkout_field_name, $selected_collection_point);
        }
        if ($selected_collection_point) {
            $collection_point = $this->collection_points_provider->get_point_by_id($selected_collection_point, $destination_country);
        }
        return $collection_point;
    }
    /**
     * @param string $checkout_field_name .
     * @param string $default .
     *
     * @return string
     */
    private function get_collection_point_from_session($checkout_field_name, $default)
    {
        return $selected_collection_point = \WC()->session->get($checkout_field_name, $default);
    }
    /**
     * Get collection point nearest to destination address.
     *
     * @param array $destination .
     *
     * @return CollectionPoint
     * @throws CollectionPointNotFoundException
     */
    private function get_collection_point_nearest_to_shipping_address(array $destination)
    {
        $address = (new \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutAddress($this->get_request(), $destination))->prepare_address();
        return $this->collection_points_provider->get_single_nearest_collection_point($address);
    }
    /**
     * Get collection point for rates.
     *
     * @param array $destination .
     *
     * @return CollectionPoint
     * @throws CollectionPointNotFoundException
     */
    public function get_collection_point_for_rates(array $destination)
    {
        $post_data = $this->prepare_post_data();
        $collection_point = $this->get_collection_point_from_posted_data_or_session($post_data, $destination['country']);
        if (null === $collection_point) {
            $collection_point = $this->get_collection_point_nearest_to_shipping_address($destination);
        }
        return $collection_point;
    }
    /**
     * Should show collection point?
     * Collection point field should be shown when shipping rate to collection point is selected.
     *
     * @return bool
     */
    private function should_show_collection_point()
    {
        $show_collection_point = \false;
        $packages = \WC()->shipping()->get_packages();
        foreach ($packages as $i => $package) {
            $chosen_method = isset(\WC()->session->chosen_shipping_methods[$i]) ? \WC()->session->chosen_shipping_methods[$i] : '';
            if (isset($package['rates'][$chosen_method])) {
                /** @var \WC_Shipping_Rate $shipping_rate */
                $shipping_rate = $package['rates'][$chosen_method];
                $shipping_rate_meta_data = $shipping_rate->get_meta_data();
                if (isset($shipping_rate_meta_data[\DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder::COLLECTION_POINT]) && $shipping_rate_meta_data[\DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder::COLLECTION_POINT] === \DhlVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder::YES) {
                    $show_collection_point = \true;
                    break;
                }
            }
        }
        return $show_collection_point;
    }
    /**
     * Get selected collection point.
     * Gets selected collection point from request or from session.
     *
     * @param $collection_points
     * @param $field_name
     *
     * @return array|mixed|string
     */
    private function get_selected_collection_point($collection_points, $field_name)
    {
        $selected_collection_point = \array_keys($collection_points)[0];
        if ($this->select_field_available) {
            $post_data = $this->prepare_post_data();
            if (isset($post_data[$field_name])) {
                $selected_collection_point = \sanitize_text_field($post_data[$field_name]);
                \WC()->session->set($field_name, $selected_collection_point);
            } else {
                $selected_collection_point = \WC()->session->get($field_name, $selected_collection_point);
            }
        }
        return $selected_collection_point;
    }
    /**
     * Get collection points nearest to destination address.
     *
     * @return CollectionPoint[]
     */
    private function get_collection_points_nearest_to_destination_address()
    {
        $address = (new \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutAddress($this->get_request()))->prepare_address();
        return $this->collection_points_provider->get_nearest_collection_points($address);
    }
    /**
     * Maybe display collection points field.
     */
    public function maybe_display_collection_points_field()
    {
        if ($this->should_show_collection_point()) {
            $field_name = \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutField::prepare_field_name_from_shipping_method_id($this->method_id);
            try {
                $nearest_collection_points = $this->get_collection_points_nearest_to_destination_address();
                $selected_collection_point = $this->get_selected_collection_point($nearest_collection_points, $field_name);
                if ($this->select_field_available) {
                    $checkout_field_class = \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutSelectField::class;
                } else {
                    $checkout_field_class = \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutHtmlField::class;
                }
            } catch (\DhlVendor\WPDesk\AbstractShipping\Exception\CollectionPointNotFoundException $e) {
                $nearest_collection_points = array();
                $selected_collection_point = '';
                $checkout_field_class = \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutHtmlField::class;
            }
            /** @var CheckoutField $checkout_field */
            $checkout_field = new $checkout_field_class($nearest_collection_points, $selected_collection_point, $this->renderer, $this->field_label, $this->unavailable_points_label, $this->field_description, $this->method_id);
            $checkout_field->render();
        }
    }
}
