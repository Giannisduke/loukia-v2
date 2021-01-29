<?php

/**
 * Settings field.
 */
namespace DhlVendor\WpDesk\WooCommerce\ShippingMethod;

use DhlVendor\WPDesk\Packer\Box;
/**
 * Display field in settings and handle posted data.
 *
 * @package WpDesk\WooCommerce\ShippingMethod
 */
class SettingsField
{
    /**
     * Field name.
     *
     * @var string
     */
    private $field_name;
    /**
     * SettingsField constructor.
     *
     * @param string $field_name .
     */
    public function __construct($field_name)
    {
        $this->field_name = $field_name;
    }
    /**
     * Get field value from post as JSON.
     *
     * @param array $posted_value .
     *
     * @return string
     */
    public function get_field_posted_value_as_json($posted_value)
    {
        if (!empty($posted_value) && \is_array($posted_value)) {
            $value = \json_encode($posted_value);
        } else {
            $value = \json_encode(array());
        }
        return $value;
    }
    /**
     * @param array $settings_field_value_as_array
     * @param Box[] $built_in_boxes_from_packer
     *
     * @return mixed
     */
    private function set_boxes_settings_from_built_in_boxes(array $settings_field_value_as_array, array $built_in_boxes_from_packer)
    {
        foreach ($settings_field_value_as_array as $key => $settings_field_box) {
            $found_built_in_box = \array_reduce($built_in_boxes_from_packer, function ($carry, \DhlVendor\WPDesk\Packer\Box $box) use($settings_field_box) {
                if ((string) $box->get_unique_id() === (string) $settings_field_box['code']) {
                    return $box;
                }
                return $carry;
            });
            if ($found_built_in_box !== null) {
                $settings_field_box['height'] = $found_built_in_box->get_height();
                $settings_field_box['length'] = $found_built_in_box->get_length();
                $settings_field_box['width'] = $found_built_in_box->get_width();
                $settings_field_box['max_weight'] = $found_built_in_box->get_max_weight();
                $settings_field_value_as_array[$key] = $settings_field_box;
            }
        }
        return $settings_field_value_as_array;
    }
    /**
     * Render field settings.
     *
     * @param string $field_title
     * @param string $tooltip_html
     * @param string $settings_field_value_as_json .
     * @param Box[]  $built_in_boxes_from_packer .
     * @param Labels $labels .
     * @param string $description .
     */
    public function render($field_title, $tooltip_html, $settings_field_value_as_json, $built_in_boxes_from_packer, $labels, $description = '')
    {
        $field_key = $this->field_name;
        $settings_field_value_as_array = \json_decode($settings_field_value_as_json, \true);
        if (empty($settings_field_value_as_array) || $settings_field_value_as_array === \false) {
            $settings_field_value_as_array = array();
        }
        $settings_field_value_as_array = $this->set_boxes_settings_from_built_in_boxes($settings_field_value_as_array, $built_in_boxes_from_packer);
        if (empty($labels)) {
            $labels = new \DhlVendor\WpDesk\WooCommerce\ShippingMethod\Labels();
        }
        $built_in_boxes = [];
        foreach ($built_in_boxes_from_packer as $built_in_box_from_packer) {
            $internal_data = $built_in_box_from_packer->get_internal_data();
            $code = $built_in_box_from_packer->get_unique_id();
            $built_in_boxes[] = \DhlVendor\WpDesk\WooCommerce\ShippingMethod\BuiltInBox::create_from_code_and_packer_box($code, $built_in_box_from_packer);
        }
        $json_value = \json_encode(\array_values($settings_field_value_as_array));
        include __DIR__ . '/views/settings-field.php';
    }
}
