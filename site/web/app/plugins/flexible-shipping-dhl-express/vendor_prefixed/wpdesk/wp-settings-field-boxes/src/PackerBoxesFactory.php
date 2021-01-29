<?php

namespace DhlVendor\WpDesk\WooCommerce\ShippingMethod;

use DhlVendor\WPDesk\Packer\Box;
class PackerBoxesFactory
{
    /**
     * Format decimal to string.
     *
     * @param $float
     *
     * @return string
     */
    private static function format_decimal($float)
    {
        return \wc_format_decimal($float);
    }
    /**
     * Prepare custom box name (with dimensions and weight).
     *
     * @param SettingsBox $setting_box
     *
     * @return string
     */
    private static function custom_box_name(\DhlVendor\WpDesk\WooCommerce\ShippingMethod\SettingsBox $setting_box)
    {
        return \sprintf(\__('Custom (%1$sx%2$sx%3$s/%4$s)', 'flexible-shipping-dhl-express'), self::format_decimal($setting_box->get_length()), self::format_decimal($setting_box->get_width()), self::format_decimal($setting_box->get_height()), self::format_decimal($setting_box->get_max_weight()));
    }
    /**
     * Prepares settings box from builtin box.
     * If there are builtin box with same code name, dimensions and max weight is overwritten by it.
     *
     * @param SettingsBox $setting_box
     * @param Box[] $built_in_boxes
     *
     * @return SettingsBox
     */
    private static function prepare_from_built_in_box(\DhlVendor\WpDesk\WooCommerce\ShippingMethod\SettingsBox $setting_box, array $built_in_boxes)
    {
        $found_built_in_box = \array_reduce($built_in_boxes, function ($carry, \DhlVendor\WPDesk\Packer\Box $box) use($setting_box) {
            if ((string) $box->get_unique_id() === (string) $setting_box->get_code()) {
                return $box;
            }
            return $carry;
        });
        if ($found_built_in_box !== null) {
            $setting_box->set_name($found_built_in_box->get_name());
            $setting_box->set_height($found_built_in_box->get_height());
            $setting_box->set_length($found_built_in_box->get_length());
            $setting_box->set_width($found_built_in_box->get_width());
            $setting_box->set_max_weight($found_built_in_box->get_max_weight());
        } else {
            $setting_box->set_name(self::custom_box_name($setting_box));
        }
        return $setting_box;
    }
    /**
     * Create
     *
     * @param string $boxes_settings
     * @param Box[] $built_in_boxes
     *
     * @return Box[]
     */
    public static function create_packer_boxes_from_settings($boxes_settings, array $built_in_boxes)
    {
        $packer_boxes = [];
        foreach (\json_decode($boxes_settings, \true) as $box_setting) {
            $setting_box = \DhlVendor\WpDesk\WooCommerce\ShippingMethod\SettingsBox::create_from_array($box_setting);
            $setting_box = static::prepare_from_built_in_box($setting_box, $built_in_boxes);
            $packer_boxes[] = new \DhlVendor\WPDesk\Packer\Box\BoxImplementation($setting_box->get_length() - $setting_box->get_padding(), $setting_box->get_width() - $setting_box->get_padding(), $setting_box->get_height() - $setting_box->get_padding(), $setting_box->get_box_weight(), $setting_box->get_max_weight(), $setting_box->get_code(), $setting_box->get_name(), array('name' => $setting_box->get_name(), 'box' => $setting_box));
        }
        return $packer_boxes;
    }
}
