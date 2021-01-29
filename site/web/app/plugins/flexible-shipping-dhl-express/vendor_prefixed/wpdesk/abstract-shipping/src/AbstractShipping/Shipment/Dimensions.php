<?php

/**
 * Simple DTO: Dimensions class.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace DhlVendor\WPDesk\AbstractShipping\Shipment;

/**
 * Class that stores the package dimensions.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
final class Dimensions
{
    const DIMENSION_UNIT_MM = 'MM';
    const DIMENSION_UNIT_CM = 'CM';
    const DIMENSION_UNIT_M = 'M';
    const DIMENSION_UNIT_IN = 'IN';
    /**
     * Height.
     *
     * @var int
     */
    public $height;
    /**
     * Width.
     *
     * @var int
     */
    public $width;
    /**
     * Length.
     *
     * @var int
     */
    public $length;
    /**
     * Dimension unit.
     *
     * @var string
     */
    public $dimensions_unit;
}
