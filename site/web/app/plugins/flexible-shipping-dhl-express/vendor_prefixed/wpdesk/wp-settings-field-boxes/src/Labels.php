<?php

/**
 * Labels.
 */
namespace DhlVendor\WpDesk\WooCommerce\ShippingMethod;

/**
 * Labels for REACT component.
 *
 * @package WpDesk\WooCommerce\ShippingMethod
 */
class Labels
{
    public $header_type = 'Type';
    public $header_length = 'Length';
    public $header_width = 'Width';
    public $header_height = 'Height';
    public $header_max_weight = 'Max Weight';
    public $header_padding = 'Padding';
    public $header_box_weight = 'Box Weight';
    public $button_delete = 'Delete';
    public $button_add = 'Add';
    /**
     * @param string $header_type
     * @param string $header_length
     * @param string $header_width
     * @param string $header_height
     * @param string $header_max_weight
     * @param string $header_padding
     * @param string $header_box_weight
     * @param string $button_delete
     * @param string $button_add
     */
    public function set_labels($header_type, $header_length, $header_width, $header_height, $header_max_weight, $header_padding, $header_box_weight, $button_delete, $button_add)
    {
        $this->header_type = $header_type;
        $this->header_length = $header_length;
        $this->header_width = $header_width;
        $this->header_height = $header_height;
        $this->header_max_weight = $header_max_weight;
        $this->header_padding = $header_padding;
        $this->header_box_weight = $header_box_weight;
        $this->button_delete = $button_delete;
        $this->button_add = $button_add;
    }
}
