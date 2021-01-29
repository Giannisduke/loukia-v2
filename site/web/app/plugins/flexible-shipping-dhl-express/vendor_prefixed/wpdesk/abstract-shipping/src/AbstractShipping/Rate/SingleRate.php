<?php

/**
 * Simple DTO: SingleRate class.
 *
 * @package WPDesk\AbstractShipping\Rate
 */
namespace DhlVendor\WPDesk\AbstractShipping\Rate;

/**
 * Define Single Rate.
 *
 * @package WPDesk\AbstractShipping\Rate
 */
final class SingleRate
{
    /**
     * Service type.
     *
     * @var string
     */
    public $service_type;
    /**
     * Service name.
     *
     * @var string
     */
    public $service_name;
    /**
     * Total charge.
     *
     * @var Money
     */
    public $total_charge;
    /**
     * Is collection point rate?
     *
     * @var bool
     */
    public $is_collection_point_rate = \false;
    /**
     * Estimated delivery date. Should be here if service implements CanReturnDeliveryDate interface.
     *
     * @var \DateTimeInterface|null
     */
    public $delivery_date;
    /**
     * Estimated number business days in transit. Should be here if service implements CanReturnDeliveryDate interface.
     *
     * @var int|null
     */
    public $business_days_in_transit;
}
