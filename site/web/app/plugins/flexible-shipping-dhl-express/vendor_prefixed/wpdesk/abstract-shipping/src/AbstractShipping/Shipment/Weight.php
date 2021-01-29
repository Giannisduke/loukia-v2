<?php

/**
 * Simple DTO: Shipment class.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace DhlVendor\WPDesk\AbstractShipping\Shipment;

/**
 * Class that stores weight data for Item.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
final class Weight
{
    const WEIGHT_UNIT_KG = 'KG';
    const WEIGHT_UNIT_G = 'G';
    const WEIGHT_UNIT_LB = 'LB';
    const WEIGHT_UNIT_LBS = 'LBS';
    const WEIGHT_UNIT_OZ = 'OZ';
    /**
     * Weight KGS by default
     *
     * @var float
     */
    public $weight;
    /**
     * Weight unit.
     *
     * @var string
     */
    public $weight_unit;
}
