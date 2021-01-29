<?php

/**
 * Simple DTO: Shipment class.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace DhlVendor\WPDesk\AbstractShipping\Shipment;

/**
 * Class that stores shipment data.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
final class Shipment
{
    /**
     * Ship from
     *
     * @var Client
     */
    public $ship_from;
    /**
     * Ship to.
     *
     * @var Client
     */
    public $ship_to;
    /**
     * Packages.
     *
     * @var Package[]
     */
    public $packages;
    /**
     * Should use insurance if possible. Declared item values will be in Item::declared_value.
     *
     * @var bool
     */
    public $insurance = \false;
    /**
     * If packed then Package::Weight, Package::Dimension should be not null.
     * Package:package_type and Package:description can still be null.
     *
     * It means that packer tried to pack items into packages and size/weight of these packages is more important for rating
     * than the size of items.
     *
     * @var bool
     */
    public $packed = \false;
}
