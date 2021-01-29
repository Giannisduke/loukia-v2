<?php

/**
 * Simple DTO: ShipmentRatingImplementation class.
 *
 * @package WPDesk\AbstractShipping
 */
namespace DhlVendor\WPDesk\AbstractShipping\Rate;

/**
 * Part of AbstractShipping package which provides uniform interface between WC and Shipment API.
 *
 * @package WPDesk\AbstractShipping
 */
class ShipmentRatingImplementation implements \DhlVendor\WPDesk\AbstractShipping\Rate\ShipmentRating
{
    /**
     * Rates.
     *
     * @var SingleRate[]
     */
    private $rates;
    /**
     * ShipmentRatingImplementation constructor.
     *
     * @param array $rates SingleRate[].
     */
    public function __construct(array $rates)
    {
        $this->rates = $rates;
    }
    /**
     * Get ratings.
     *
     * @return SingleRate[]
     */
    public function get_ratings()
    {
        return $this->rates;
    }
}
