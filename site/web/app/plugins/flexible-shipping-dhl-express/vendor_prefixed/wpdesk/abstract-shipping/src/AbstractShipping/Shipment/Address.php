<?php

/**
 * Simple DTO: Address class.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace DhlVendor\WPDesk\AbstractShipping\Shipment;

/**
 * Class that stores the customer's address data.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
final class Address
{
    /**
     * Adress line 2.
     *
     * @var string
     */
    public $address_line1;
    /**
     * Adress line 2.
     *
     * @var string
     */
    public $address_line2;
    /**
     * Postal code.
     *
     * @var string
     */
    public $postal_code;
    /**
     * City.
     *
     * @var string
     */
    public $city;
    /**
     * State code.
     *
     * @var string
     */
    public $state_code;
    /**
     * Country code.
     *
     * @var string
     */
    public $country_code;
    /**
     * Street lines
     *
     * @var array
     */
    public $street_lines = [];
}
