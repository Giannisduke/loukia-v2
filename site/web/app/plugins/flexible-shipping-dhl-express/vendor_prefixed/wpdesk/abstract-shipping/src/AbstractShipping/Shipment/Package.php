<?php

/**
 * Simple DTO: Package class.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace DhlVendor\WPDesk\AbstractShipping\Shipment;

/**
 * Class that stores packages data for shipment.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
final class Package
{
    /** Items packed in this package.
     *
     * @var Item[]
     */
    public $items;
    /**
     * Item weight. Can be null if not packed.
     *
     * @var Weight|null
     */
    public $weight;
    /**
     * Item dimensions. Can be null if not packed.
     *
     * @var Dimensions|null
     */
    public $dimensions;
    /**
     * Packages can be a special packages with type ie. CUBE_BOX . If not set then shipment should use custom type.
     *
     * @var string|null
     */
    public $package_type;
    /**
     * Packages can have a descriptive name.
     *
     * @var string|null
     */
    public $description;
}
