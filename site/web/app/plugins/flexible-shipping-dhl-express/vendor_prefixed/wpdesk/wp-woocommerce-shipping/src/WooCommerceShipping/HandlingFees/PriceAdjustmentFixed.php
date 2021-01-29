<?php

/**
 * Price fixed adjustment.
 *
 * @package WPDesk\WooCommerceShipping\HandlingFees
 */
namespace DhlVendor\WPDesk\WooCommerceShipping\HandlingFees;

/**
 * Can apply fixed value to price.
 */
class PriceAdjustmentFixed implements \DhlVendor\WPDesk\WooCommerceShipping\HandlingFees\PriceAdjustment
{
    const ADJUSTMENT_TYPE = 'fixed';
    /**
     * Adjustment value.
     *
     * @var float
     */
    private $adjustment_value;
    /**
     * Rounding
     *
     * @var float
     */
    private $rounding;
    /**
     * PriceAdjustmentFixed constructor.
     *
     * @param float $adjustment_value Adjustment value.
     * @param float $rounding Rounding.
     */
    public function __construct($adjustment_value, $rounding)
    {
        $this->adjustment_value = $adjustment_value;
        $this->rounding = $rounding;
    }
    /**
     * @param float $price
     *
     * @return float
     */
    public function apply_on_price($price)
    {
        return \round($price + $this->adjustment_value, $this->rounding);
    }
}
