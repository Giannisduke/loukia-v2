<?php

/**
 * Box.
 */
namespace DhlVendor\WpDesk\WooCommerce\ShippingMethod;

/**
 * Settings for box from saved settings.
 *
 * @package WpDesk\WooCommerce\ShippingMethod
 */
class SettingsBox extends \DhlVendor\WpDesk\WooCommerce\ShippingMethod\AbstractBox implements \JsonSerializable
{
    const BOX_WEIGHT = 'box_weight';
    const PADDING = 'padding';
    /**
     * @var null|float
     */
    private $box_weight;
    /**
     * @var int
     */
    private $padding = 0;
    /**
     * @param array $box_array .
     *
     * @return SettingsBox
     */
    public static function create_from_array(array $box_array)
    {
        $box = new \DhlVendor\WpDesk\WooCommerce\ShippingMethod\SettingsBox();
        if (isset($box_array[self::CODE])) {
            $box->set_code($box_array[self::CODE]);
        }
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
        if (isset($box_array[self::BOX_WEIGHT])) {
            $box->set_box_weight($box_array[self::BOX_WEIGHT]);
        }
        if (isset($box_array[self::PADDING])) {
            $box->set_padding($box_array[self::PADDING]);
        }
        return $box;
    }
    /**
     * @return mixed
     */
    public function get_box_weight()
    {
        return $this->box_weight;
    }
    /**
     * @param mixed $box_weight
     */
    public function set_box_weight($box_weight)
    {
        $this->box_weight = $box_weight;
    }
    /**
     * @return int
     */
    public function get_padding()
    {
        return $this->padding;
    }
    /**
     * @param int $padding
     */
    public function set_padding($padding)
    {
        $this->padding = $padding;
    }
    /**
     * JSON serialize.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $serialized = parent::jsonSerialize();
        $serialized[self::BOX_WEIGHT] = $this->get_box_weight();
        $serialized[self::PADDING] = $this->get_padding();
        return $serialized;
    }
}
