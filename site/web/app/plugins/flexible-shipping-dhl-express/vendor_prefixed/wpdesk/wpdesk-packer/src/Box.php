<?php

namespace DhlVendor\WPDesk\Packer;

/**
 * Box is a place into which we can pack items
 *
 * @package WPDesk\Packer
 */
interface Box extends \DhlVendor\WPDesk\Packer\Item
{
    /** @return float|null */
    public function get_max_weight();
    /** @return string */
    public function get_name();
    /** @return string */
    public function get_unique_id();
}
