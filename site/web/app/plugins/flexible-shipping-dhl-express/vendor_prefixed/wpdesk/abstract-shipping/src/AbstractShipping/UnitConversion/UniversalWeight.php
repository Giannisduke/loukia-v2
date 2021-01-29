<?php

/**
 * UnitConversion: Weight Conversion.
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace DhlVendor\WPDesk\AbstractShipping\UnitConversion;

use DhlVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use DhlVendor\WPDesk\AbstractShipping\Shipment\Weight;
/**
 * Can convert weight between different measure types
 */
class UniversalWeight
{
    /**
     * Weight.
     *
     * @var float
     */
    private $weight;
    const UNIT_CALC = [\DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB => 453.59237, \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_KG => 1000, \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_OZ => 28.34952];
    /**
     * @var int
     */
    private $precision;
    /**
     * WeightConverter constructor.
     *
     * @param float  $weight Weight.
     * @param string $unit   Unit.
     * @param int $precision .
     *
     * @throws UnitConversionException Weight exception.
     */
    public function __construct($weight, $unit, $precision = 3)
    {
        $this->precision = $precision;
        $this->weight = $this->to_grams($weight, $unit);
    }
    /**
     * Unify all units to grams.
     *
     * @param float  $weight Weight.
     * @param string $unit   Unit.
     *
     * @return float
     * @throws \WPDesk\AbstractShipping\Exception\UnitConversionException Unit
     *                                                                    Conversion.
     */
    private function to_grams($weight, $unit)
    {
        $unit = \strtoupper($unit);
        if (\DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LBS === $unit) {
            $unit = \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB;
        }
        if (\DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_G === $unit) {
            return $weight;
        }
        $calc = self::UNIT_CALC[$unit];
        switch ($unit) {
            case \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_KG:
                return \round($weight * $calc, $this->precision);
            case \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB:
                return \round($weight * $calc, $this->precision);
            case \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_OZ:
                return \round($weight * $calc, $this->precision);
        }
        throw new \DhlVendor\WPDesk\AbstractShipping\Exception\UnitConversionException(\sprintf('Can\'t support "%s" unit', $unit));
    }
    /**
     * Convert to target unit. Returns 0 if confused.
     *
     * @param string $target_unit Target unit.
     * @param int $precision .
     *
     * @return float
     *
     * @throws UnitConversionException Weight exception.
     */
    public function as_unit_rounded($target_unit, $precision = 2)
    {
        $target_unit = \strtoupper($target_unit);
        $calc = self::UNIT_CALC[$target_unit];
        switch ($target_unit) {
            case \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_G:
                return $this->weight;
            case \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_KG:
                return \round($this->weight / $calc, $precision);
            case \DhlVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB:
                return \round($this->weight / $calc, $precision);
            default:
                throw new \DhlVendor\WPDesk\AbstractShipping\Exception\UnitConversionException(\__('Can\'t convert weight to target unit.', 'flexible-shipping-dhl-express'));
        }
    }
}
