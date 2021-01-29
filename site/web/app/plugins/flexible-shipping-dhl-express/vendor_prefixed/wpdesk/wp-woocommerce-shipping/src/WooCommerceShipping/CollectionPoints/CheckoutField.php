<?php

/**
 * Collection Points: CheckoutField class.
 *
 * @package WPDesk\WooCommerceShipping\CollectionPoints
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints;

use DhlVendor\WPDesk\AbstractShipping\CollectionPoints\CollectionPoint;
use DhlVendor\WPDesk\View\Renderer\Renderer;
/**
 * Can render view for collection points select field.
 *
 * @package WPDesk\CustomFields
 */
abstract class CheckoutField
{
    /**
     * Collection points.
     *
     * @var CollectionPoint[]
     */
    protected $collection_points;
    /**
     * Selected collection point.
     *
     * @var string
     */
    protected $selected_collection_point;
    /**
     * Template name.
     *
     * @var string
     */
    protected $template_name;
    /**
     * Renderer.
     *
     * @var Renderer
     */
    protected $renderer;
    /**
     * Field label.
     *
     * @var string
     */
    protected $label;
    /**
     * Unavailable points label.
     *
     * @var string
     */
    protected $unavailable_points_label;
    /**
     * Field description.
     *
     * @var string
     */
    protected $description;
    /**
     * Shipping method ID.
     *
     * @var string
     */
    protected $shipping_method_id;
    /**
     * CheckoutField constructor.
     *
     * @param CollectionPoint[] $collection_points .
     * @param string            $selected_collection_point .
     * @param Renderer          $renderer .
     * @param string            $label .
     * @param string            $unavailable_points_label .
     * @param string            $description .
     * @param string            $shipping_method_id .
     */
    public function __construct(array $collection_points, $selected_collection_point, \DhlVendor\WPDesk\View\Renderer\Renderer $renderer, $label, $unavailable_points_label, $description, $shipping_method_id)
    {
        $this->collection_points = $collection_points;
        $this->selected_collection_point = $selected_collection_point;
        $this->renderer = $renderer;
        $this->label = $label;
        $this->unavailable_points_label = $unavailable_points_label;
        $this->description = $description;
        $this->shipping_method_id = $shipping_method_id;
    }
    /**
     * Prepare collection point options.
     *
     * @return array
     */
    private function prepare_collection_point_options()
    {
        $options = array();
        /** @var CollectionPoint $collection_point */
        $collection_point_formatter = new \DhlVendor\WPDesk\WooCommerceShipping\CollectionPoints\CollectionPointFormatter();
        foreach ($this->collection_points as $collection_point) {
            $options[$collection_point->collection_point_id] = $collection_point_formatter->get_collection_point_as_label($collection_point);
        }
        return $options;
    }
    /**
     * Prepare field name from shipping method ID.
     *
     * @param $shipping_method_id .
     *
     * @return string
     */
    public static function prepare_field_name_from_shipping_method_id($shipping_method_id)
    {
        return $shipping_method_id . '_collection_point';
    }
    /**
     * Prepare params.
     *
     * @return array
     */
    protected function prepare_params()
    {
        $field_name = self::prepare_field_name_from_shipping_method_id($this->shipping_method_id);
        $select_options = $this->prepare_collection_point_options();
        return array('select_options' => $select_options, 'selected_access_point' => $this->selected_collection_point, 'label' => $this->label, 'unavailable_points_label' => $this->unavailable_points_label, 'description' => $this->description, 'shipping_method_id' => $this->shipping_method_id, 'field_name' => $field_name);
    }
    /**
     * Render.
     */
    public function render()
    {
        echo $this->renderer->render($this->template_name, $this->prepare_params());
    }
}
