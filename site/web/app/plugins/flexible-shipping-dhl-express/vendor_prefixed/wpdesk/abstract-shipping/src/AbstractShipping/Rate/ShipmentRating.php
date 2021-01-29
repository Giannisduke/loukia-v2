<?php

/**
 * Simple DTO: ShipmentRating interface.
 *
 * @package WPDesk\AbstractShipping\Rate
 */
namespace DhlVendor\WPDesk\AbstractShipping\Rate;

/**
 * Interface for classes that store shipment prices. It will be returned by CanRate::get_rates.
 *
 * @package WPDesk\AbstractShipping\Rate
 */
interface ShipmentRating
{
    /**
     * Get ratings.
     *
     * @return SingleRate[]
     */
    public function get_ratings();
}
