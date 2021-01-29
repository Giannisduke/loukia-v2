<?php

/**
 * Abstract Box.
 */
namespace DhlVendor\WpDesk\WooCommerce\ShippingMethod;

/**
 * Class Box
 * @package WpDesk\WooCommerce\ShippingMethod
 */
abstract class AbstractBox implements \JsonSerializable
{
    const CODE = 'code';
    const NAME = 'name';
    const LENGTH = 'length';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const MAX_WEIGHT = 'max_weight';
    /**
     * @var string
     */
    private $code = '';
    /**
     * @var string
     */
    private $name = '';
    /**
     * @var null|float
     */
    private $length;
    /**
     * @var null|float
     */
    private $width;
    /**
     * @var null|float
     */
    private $height;
    /**
     * @var null|float
     */
    private $max_weight;
    /**
     * @return string
     */
    public function get_code()
    {
        return $this->code;
    }
    /**
     * @param string $code
     */
    public function set_code($code)
    {
        $this->code = $code;
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }
    /**
     * @return float|null
     */
    public function get_length()
    {
        return $this->length;
    }
    /**
     * @param float|null $length
     */
    public function set_length($length)
    {
        $this->length = $length;
    }
    /**
     * @return float|null
     */
    public function get_width()
    {
        return $this->width;
    }
    /**
     * @param float|null $width
     */
    public function set_width($width)
    {
        $this->width = $width;
    }
    /**
     * @return float|null
     */
    public function get_height()
    {
        return $this->height;
    }
    /**
     * @param float|null $height
     */
    public function set_height($height)
    {
        $this->height = $height;
    }
    /**
     * @return mixed
     */
    public function get_max_weight()
    {
        return $this->max_weight;
    }
    /**
     * @param mixed $max_weight
     */
    public function set_max_weight($max_weight)
    {
        $this->max_weight = $max_weight;
    }
    /**
     * JSON serialize.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [self::CODE => $this->get_code(), self::NAME => $this->get_name(), self::LENGTH => $this->get_length(), self::WIDTH => $this->get_width(), self::HEIGHT => $this->get_height(), self::MAX_WEIGHT => $this->get_max_weight()];
    }
}
