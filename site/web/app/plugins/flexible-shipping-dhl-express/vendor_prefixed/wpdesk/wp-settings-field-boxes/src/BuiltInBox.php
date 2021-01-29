<?php

/**
 * Built in Box.
 */
namespace DhlVendor\WpDesk\WooCommerce\ShippingMethod;

use DhlVendor\WPDesk\Packer\Box;
/**
 * Built in box properties.
 *
 * @package WpDesk\WooCommerce\ShippingMethod
 */
class BuiltInBox extends \DhlVendor\WpDesk\WooCommerce\ShippingMethod\AbstractBox implements \JsonSerializable
{
    /**
     * Create from code and array.
     *
     * @param string $code .
     * @param array  $box_array .
     *
     * @return BuiltInBox
     */
    public static function create_from_code_and_array($code, array $box_array)
    {
        $box = new self();
        $box->set_code($code);
        if (isset($box_array[self::NAME])) {
            $box->set_name($box_array[self::NAME]);
        }
        if (isset($box_array[self::LENGTH])) {
            $box->set_length($box_array[self::LENGTH]);
        }
        if (isset($box_array[self::WIDTH])) {
            $box->set_width($box_array[self::WIDTH]);
        }
        if (isset($box_array[self::HEIGHT])) {
            $box->set_height($box_array[self::HEIGHT]);
        }
        if (isset($box_array[self::MAX_WEIGHT])) {
            $box->set_max_weight($box_array[self::MAX_WEIGHT]);
        }
        return $box;
    }
    /**
     * Create from code and Packer Box.
     *
     * @param string $code .
     * @param Box    $packer_box .
     *
     * @return BuiltInBox
     */
    public static function create_from_code_and_packer_box($code, \DhlVendor\WPDesk\Packer\Box $packer_box)
    {
        $box = new self();
        $box->set_code($code);
        $internal_data = $packer_box->get_internal_data();
        $box->set_name($packer_box->get_name());
        $box->set_height($packer_box->get_height());
        $box->set_length($packer_box->get_length());
        $box->set_width($packer_box->get_width());
        $box->set_max_weight($packer_box->get_max_weight());
        return $box;
    }
}
